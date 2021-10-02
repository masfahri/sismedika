<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Meja;
use App\Models\Order;
use App\Constants\Flag;
use App\Models\OrdeTemp;
use App\Models\OrderTemp;
use Illuminate\Http\Request;
use App\Models\ViewTotalHarga;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Models\OrderDetail;
use App\Models\ViewTotalHargabyIDFromOrders;

class OrderController extends Controller
{
    public function __construct() {
        $this->pageTitle = 'Order';
        $this->itemAktif = Flag::ITEM_ACTIVE;
        $this->mejaAktif = Flag::ITEM_ACTIVE;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subtotal_orderTemps = ViewTotalHarga::first()->subtotal;
        $items = Item::all();
        $order_temps = OrderTemp::all();
        $pageTitle = $this->pageTitle;
        $active = $this->itemAktif;
        $meja = Meja::where('flag', 'avail')->pluck('nomor_meja', 'id');
        activity()->log('Melihat Semua Pesanan Aktif');
        return view('back.pages.Order.index', compact('subtotal_orderTemps', 'pageTitle', 'active', 'items', 'meja', 'order_temps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request, Order $orderModel, OrderTemp $orderTemp, OrderDetail $orderDetailModel)
    {
        try {
            DB::beginTransaction();
            $orderTemps = $orderTemp::all();
            $order_id = $this->getLastCode(Order::class, 'order_id', 'PSN-', 4);
            $invoice = $this->getLastCode(Order::class, 'invoice', 'PSN'.date('Ymd').'-', 12);
            $orderModel::create([
                'user_id' => auth()->user()->id,
                'order_id' => $order_id,
                'invoice' => $invoice,
                'nama_pelanggan' => $request->nama_pelanggan,
                'total_bayar' => $request->subtotal,
                'bayar' => $request->jumlah_bayar,
                'catatan' => $request->catatan,
                'nomor_meja' => $request->nomor_meja,
                'flag' => Flag::ORDER_ACTIVE
            ]);

            foreach ($orderTemps as $item) {
                $orderDetailModel::create([
                    'order_id' => $order_id,
                    'kode_item' => $item->kode_item,
                    'qty' => $item->qty,
                    'subtotal' => $item->sub_total
                ]);
            };
            Meja::find($request->nomor_meja)->update([
                'flag' => Flag::MEJA_RSRVD
            ]);
            OrderTemp::truncate();
            activity()->log('Menambahkan Pesanan');
            DB::commit();
            return redirect()->route('pelayan.receipt.show',$order_id)->with('success', 'Pesanan Telah Dibuat');
        } catch (\Throwable $th) {
            DB::rollback();dd($th->getMessage());

            return redirect()->route('pelayan.order.index')->with('Error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $total_harga = ViewTotalHargabyIDFromOrders::where('user_id', auth()->user()->id)->first();
        activity()->log('Melihat Pesanan ');
        return view('back.pages.Order.show', compact('order', 'total_harga'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * Get All Order List
     *
     * @param \App\Model\Order $order
     * @return
     */
    public function ListOrder(Order $order)
    {
        auth()->user()->hasRole('pelayan')
            ? $orders = $order::where('user_id', auth()->user()->id)->get()
            : $orders = $order::all();
        $pageTitle = 'List Pesanan';
        activity()->log('Melihat Semua Pesanan ');
        return view('back.pages.Order.list', compact('orders', 'pageTitle'));
    }

    public function receipt($id)
    {
        $data = Order::where('order_id', $id)->first();
        return view('back.report.receipt', compact('data'));
    }
}
