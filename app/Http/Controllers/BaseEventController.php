<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * BaseEventController
 * 
 * Abstract class yang menjadi template untuk semua controller yang menampilkan daftar event.
 * Mengimplementasikan Template Method Pattern untuk menghindari duplikasi kode
 * dan menunjukkan konsep POLYMORPHISM (Method Overriding).
 */
abstract class BaseEventController extends Controller
{
    /**
     * Method abstrak - WAJIB di-override oleh child class.
     * Setiap child class akan mengembalikan Query Builder yang berbeda:
     * - Admin: melihat SEMUA event
     * - Panitia: hanya melihat event MILIKNYA sendiri
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function getBaseQuery(Request $request);

    /**
     * Method abstrak - WAJIB di-override oleh child class.
     * Setiap child class memiliki view yang berbeda dan data tambahan yang berbeda:
     * - Admin: mengirim $categories & $panitia ke view
     * - Panitia: hanya mengirim $events
     * 
     * @param mixed $events
     * @param Request $request
     * @return \Illuminate\View\View
     */
    abstract protected function renderIndexView($events, Request $request);

    /**
     * Method INDEX utama.
     * Logika umum (filter, search, pagination) DITULIS SEKALI di sini.
     * 
     * Konsep POLYMORPHISM: 
     * Method ini memanggil $this->getBaseQuery() dan $this->renderIndexView().
     * Karena $this merujuk ke child class, maka perilaku ditentukan oleh child class
     * (Runtime Polymorphism / Dynamic Binding).
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ==========================================
        // 1. Dapatkan query dari child class (POLYMORPHISM)
        // ==========================================
        $query = $this->getBaseQuery($request)
            ->with(['category', 'panitia'])
            ->withCount('registrations');

        // ==========================================
        // 2. Filter berdasarkan status (logika UMUM)
        // ==========================================
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ==========================================
        // 3. Filter berdasarkan kategori (logika UMUM)
        // ==========================================
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ==========================================
        // 4. Filter berdasarkan panitia (logika UMUM)
        // ==========================================
        if ($request->filled('panitia_id')) {
            $query->where('panitia_id', $request->panitia_id);
        }

        // ==========================================
        // 5. Filter berdasarkan waktu (logika UMUM)
        // ==========================================
        if ($request->filled('time_filter')) {
            if ($request->time_filter === 'upcoming') {
                $query->whereDate('event_date', '>=', now());
            } elseif ($request->time_filter === 'past') {
                $query->whereDate('event_date', '<', now());
            }
        }

        // ==========================================
        // 6. Filter pencarian (logika UMUM)
        // ==========================================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // ==========================================
        // 7. Eksekusi query dengan pagination
        // ==========================================
        $events = $query->orderBy('event_date', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // ==========================================
        // 8. Render view (POLYMORPHISM)
        // ==========================================
        return $this->renderIndexView($events, $request);
    }
}