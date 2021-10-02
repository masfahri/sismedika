<?php

namespace App\Http\Controllers;

use App\Http\Requests\PegawaiRequest;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function __construct() {
        $this->index = 'admin.pegawai.index';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pegawais = Pegawai::all();
        return view('back.pages.pegawai.index', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PegawaiRequest $request)
    {
        try {
            DB::beginTransaction();
            Pegawai::create(collect($request->validated())->filter()->toArray());
            DB::commit();
            return redirect()->route($this->index)->with('success', 'Pegawai '.$request->flag.' updated successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route($this->index)->with('error', 'Pegawai '.$request->flag.' updated un-successfully bcs '.$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function show(Pegawai $pegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pegawais = Pegawai::all();
        $pegawai = Pegawai::findOrFail($id);
        return view('back.pages.pegawai.edit', compact('pegawais', 'pegawai'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $pegawai = Pegawai::findOrFail($id);
            $pegawai->nama       = $request->nama;
            $pegawai->tanggal_lahir      = $request->tanggal_lahir;
            $pegawai->gaji = $request->gaji;

            $pegawai->save();
            DB::commit();
            return redirect()->route($this->index)->with('success', 'Pegawai '.$request->flag.' updated successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route($this->index)->with('error', 'Pegawai '.$request->flag.' updated un-successfully bcs '.$th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pegawai = Pegawai::findOrFail($id);
            $pegawai->delete();
            DB::commit();
            return redirect()->route($this->index)->with('success', 'Pegawai updated successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route($this->index)->with('error', 'Pegawai updated un-successfully bcs '.$th->getMessage());
        }
    }
}
