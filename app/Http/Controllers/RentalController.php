<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Exception;
use Illuminate\Routing\Controller;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data= Rental::orderByDesc('updated_at')->get(); // mengambil semua data

        if ($request->query('search_supir')){// membuat pencarian berdasarkan Supir
            $search = $request->query('search_supir');

            $data = Rental::where('supir', $search)->orderByDesc('updated_at')->get();
            if($request->query('limit')) {
                $limit = $request->query('limit');

                $data = Rental::where('supir', $search)->limit($limit)->get();
            }
        }

        if($data){
            return ApiFormatter::createAPI(200, 'berhasil', $data);
            // return dd($data);
         }else{
            return ApiFormatter::createAPI(400, 'Failed');
        }
    }

    public function createToken()
    {
        return csrf_token();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'alamat' => 'required',
                'type' => 'required',
                'waktu_jam' => 'required',
                'jam_mulai' => 'required',
                'supir' => 'required',
            ]);

            $total_harga = $request->waktu_jam * 150000;
            $rental = Rental::create([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'type' => $request->type,
                'waktu_jam' => $request->waktu_jam,
                'total_harga' => $total_harga,
                'jam_mulai' => $request->jam_mulai,
                'supir' => $request->supir,
            ]);

            $getDataSaved = Rental::where('id', $rental->id)->first();

            if ($getDataSaved) {
                return ApiFormatter::createApi(200, 'success', $getDataSaved);
            } else {
                return ApiFormatter::createApi(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createApi(400, 'failed', $error);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rental $rental, $id)
    {
        try {
            $rentalDetail = Rental::where('id', $id)->first();
            if ($rentalDetail) {
                return ApiFormatter::createAPI(200, 'success', $rentalDetail);
            } else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'jam_selesai' => 'required',
                'tempat_tujuan' => 'required',
            ]);

            $rentals = Rental::find($id);
            $rentals->update([
                'jam_selesai' => $request->jam_selesai,
                'tempat_tujuan' => $request->tempat_tujuan,
                'riwayat_perjalanan' => "Dimulai pada saat jam $rentals->jam_mulai dengan titik awal berada di $rentals->alamat, dan diakhiri pada jam $request->jam_selesai dengan tempat tujuan di $request->tempat_tujuan",
                'status' => 'selesai',
            ]);

            $Data = Rental::where('id', $rentals->id)->first();
            if ($Data) {
                return ApiFormatter::createAPI(200, 'success', $Data);
            }else{
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $rental = Rental::find($id);
            $cekBerhasil = $rental->delete();
            if ($cekBerhasil) {
                return ApiFormatter::createAPI(200, 'success', 'Data Terhapus!');
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error');
        }
    }

    //Menampilkan seluruh data yang telah dihapus sementara
   public function trash(Request $request)
   {
    try {
        $rentals = Rental::onlyTrashed()->orderByDesc('deleted_at')->get();
        if ($rentals) {
            return ApiFormatter::createApi(200, 'success', $rentals);
        }else {
            return ApiFormatter::createApi(400, 'failed');
        }
        } catch (Exception $error) {
        return ApiFormatter::createApi(400, 'error', $error->getMessage());
        }
   }

   //mengembalikan data yang terhapus
   public function restore($id)
   {
    try {
        $rental = Rental::onlyTrashed()->where('id', $id);
        $rental->restore();
        $dataRestore = Rental::where('id', $id)->first();
        if ($dataRestore) {
            return ApiFormatter::createApi(200, 'success', $dataRestore);
        }else {
            return ApiFormatter::createApi(400, 'failed');
        }
    } catch (Exception $error) {
        return ApiFormatter::createApi(400, 'error', $error->getMessage());
    }
   }


   //menghapus data permanen
   public function permanentDelete($id)
   {
    try {
            $rentals = Rental::onlyTrashed()->where('id', $id);
            $proses = $rentals->forceDelete();
            if ($proses) {
                return ApiFormatter::createAPI(200, 'success', 'Data berhasil dihapus (permanent) !');
            } else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
   }

}
