<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Halaman Inventaris (Dapat dilihat oleh Murid, Admin, dan Dosen)
     */
    public function index(Request $request)
    {
        $inventories = Inventory::with(['items', 'usages.item'])
            ->when($request->location_type, fn ($q) => $q->where('location_type', $request->location_type))
            ->latest()
            ->get();

        $atributList = Inventory::ATRIBUT_LIST;

        return view('inventaris.index', compact('inventories', 'atributList'));
    }

    /**
     * Upload / Tambah Inventory Lokasi & Atribut (HANYA Admin / Dosen)
     */
    public function store(Request $request)
    {
        $this->authorizeAdminOrDosen();

        $validated = $request->validate([
            'lokasi' => 'required|string|max:150',
            'location_type' => 'required|in:gedung,taman,lapangan',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'atribut' => ['required', Rule::in(Inventory::ATRIBUT_LIST)],
            'jumlah' => 'required|integer|min:1',
        ], [
            'atribut.in' => 'Jenis atribut harus dipilih dari daftar resmi inventaris bencana.',
        ]);

        $inventory = DB::transaction(function () use ($validated) {
            // Jika lokasi sudah ada di peta, pakai titik lokasi tersebut agar ikon BACKPACK tidak dobel
            $inventory = Inventory::firstOrCreate(
                [
                    'lokasi' => $validated['lokasi'],
                    'location_type' => $validated['location_type'],
                ],
                [
                    'user_id' => Auth::id(),
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                ]
            );

            // Update koordinat terbaru jika perlu & tambah/akumulasi jumlah atribut
            $item = InventoryItem::where('inventory_id', $inventory->id)
                ->where('atribut', $validated['atribut'])
                ->first();

            if ($item) {
                $item->increment('jumlah', $validated['jumlah'], ['updated_by' => Auth::id()]);
            } else {
                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'atribut' => $validated['atribut'],
                    'jumlah' => $validated['jumlah'],
                    'updated_by' => Auth::id(),
                ]);
            }

            return $inventory->load('items');
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data inventaris berhasil disimpan dan disinkronkan ke peta!',
                'data' => $inventory,
            ]);
        }

        return redirect()->back()->with('success', 'Inventaris berhasil ditambahkan dan disinkronkan ke peta!');
    }

    /**
     * Update jumlah stok manual (HANYA Admin / Dosen)
     */
    public function updateStock(Request $request, InventoryItem $item)
    {
        $this->authorizeAdminOrDosen();

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:0',
        ]);

        $item->update([
            'jumlah' => $validated['jumlah'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Stok {$item->atribut} berhasil diperbarui.");
    }

    /**
     * Endpoint API untuk merender ikon "BACKPACK" ke view.map (OpenLayers)
     * Polanya sama persis dengan ReportController@getMapPoints di PBI 4
     */
    public function getMapPoints()
    {
        $inventories = Inventory::with('items')
            ->whereHas('items') // Hanya lokasi yang sudah memiliki item
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $inventories->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'lokasi' => $inv->lokasi,
                    'location_type' => $inv->location_type,
                    'lat' => (float) $inv->latitude,
                    'lng' => (float) $inv->longitude,
                    'total_stok' => $inv->items->sum('jumlah'),
                    'items' => $inv->items->map(fn ($item) => [
                        'id' => $item->id,
                        'atribut' => $item->atribut,
                        'jumlah' => $item->jumlah,
                    ]),
                    'updated_at' => $inv->updated_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Form Pemakaian Inventory saat pengguna mengambil atribut di lokasi
     */
    public function storeUsage(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'nama' => 'required|string|max:100',
            'nim' => ['required', 'regex:/^[0-9]{10}$/'], // Konsisten 10 digit Tel-U seperti PBI 4
            'jumlah_diambil' => 'required|integer|min:1',
            'alasan' => 'required|string|min:5',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // Max 10MB
        ], [
            'nim.regex' => 'NIM harus tepat 10 digit angka resmi Telkom University (contoh: 1301213088).',
            'photo.max' => 'Ukuran foto maksimal adalah 10 MB!',
            'photo.image' => 'File yang diunggah harus berupa gambar (JPG, PNG, atau WEBP).',
        ]);

        // Simpan file foto ke storage/app/public/inventory_usages
        $photoPath = $request->file('photo')->store('inventory_usages', 'public');

        try {
            $usage = DB::transaction(function () use ($validated, $inventory, $photoPath) {
                // Lock row agar tidak terjadi race condition jika diambil bersamaan
                $item = InventoryItem::where('id', $validated['inventory_item_id'])
                    ->where('inventory_id', $inventory->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item->jumlah < $validated['jumlah_diambil']) {
                    throw new \Exception("Stok {$item->atribut} tidak mencukupi (tersedia: {$item->jumlah}).");
                }

                // Kurangi stok
                $item->decrement('jumlah', $validated['jumlah_diambil']);

                // Catat di tabel pemakaian
                return InventoryUsage::create([
                    'inventory_id' => $inventory->id,
                    'inventory_item_id' => $item->id,
                    'user_id' => Auth::id() ?? null,
                    'nama' => $validated['nama'],
                    'nim' => $validated['nim'],
                    'jumlah_diambil' => $validated['jumlah_diambil'],
                    'alasan' => $validated['alasan'],
                    'photo_path' => $photoPath,
                ]);
            });
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->withErrors(['jumlah_diambil' => $e->getMessage()])->withInput();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Form pemakaian berhasil dicatat dan stok telah diperbarui!',
                'data' => $usage,
            ]);
        }

        return redirect()->back()->with('success', 'Pemakaian atribut berhasil dicatat dan stok otomatis berkurang!');
    }

    /**
     * Guard Role: Hanya Admin atau Dosen yang boleh mengelola inventaris
     */
    private function authorizeAdminOrDosen(): void
    {
        $user = Auth::user();
        if (! $user || ! in_array(strtolower($user->role ?? ''), ['admin', 'dosen'])) {
            abort(403, 'Akses ditolak. Hanya Admin atau Dosen yang dapat mengisi/mengubah inventaris.');
        }
    }
}
