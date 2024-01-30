<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteRequest;
use App\Models\CateModel;
use App\Models\ProductModel;
use App\Rules\PhoneRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\CartModel;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use LengthException;

class CartController extends Controller
{
    private const IS_ADMIN = 1;
    protected $prd;
    protected $cate;
    protected $subtotal;
    protected $Cartcheck;
    public function __construct()
    {
        $this->prd = new ProductModel();
        $this->cate = new CateModel();
        $this->Cartcheck = new CartModel();
    }
    public function getCart()
    {
        $cart = \Cart::getContent();
        $total = \Cart::getSubTotalWithoutConditions();
        $email = Auth::user()->email;
        $users = DB::table('table_user')
            ->where('email', $email)
            ->first();
        // dd($users);

        $quanity = \Cart::getTotalQuantity();
        $list = $this->cate->list();
        $subtotals = $this->subtotal;
        return view('client.cart', compact('cart', 'list', 'quanity', 'total', 'users'));
    }
    public function addToCart($id)
    {
        $prd = $this->prd->getoneProduct($id);
        // dd($prd);
        \Cart::add([
            'id' => $id,
            'name' => $prd->name_product,
            'price' => $prd->price,
            'quantity' => 1,
            'attributes' => array(
                'img' => $prd->img
            )
        ]);
        return back()->with('successcart', 'Product is Added to Cart Successfully !');
    }
    public function DeleteCart(Request $request)
    {
        \Cart::remove($request->id);
        return  back()->with('success', 'Bạn Đã Xóa Thành Công Sản Phẩm');
    }
    public function DeleteCartALL()
    {
        \Cart::clear();
        return  back()->with('success', 'Bạn Đã Xóa Thành Công ALL Sản Phẩm');
    }
    public function UpdateCart(Request $request)
    {
        // dd($request->all());
        $quanity = $request->quantity;
        $id = $request->hiden_id;

        // dd($id, $quanity);
        $update = \Cart::update($id, [
            'quantity' =>  $quanity,
        ]);
        return back()->with('success', 'Bạn đã update thành công sản phẩm ở trong giỏ hàng!!');
    }
    public function getviewCheckout(Request $request)
    {
        $list = $this->cate->list();

        $email = Auth::user()->email;
        // dd($email);
        $viewcart = $this->Cartcheck->listCart($email);
        $quanity = $request->quantity;
        return view('client.complete', compact('quanity', 'list', 'viewcart'));
    }
    public function SendMail(Request $request, CompleteRequest $ValidateMent)
    {
        $iduser = Auth::user()->id;
        $ValidateMent->validated();
        $cart = \Cart::getcontent();
        $total = \Cart::getTotal();
        if ($cart->count() > 0) {
            $data = [
                'name' => json_encode($cart),
                'mail' => $request->email,
                'address' => $request->address,
                'phone' => $request->phone,
                'status' => 0,
                'name_prd' => $request->name_prd,
                'price_prd' => $request->price_prd,
                'quanity_prd' => $request->quantity,
                'img_prd' => $request->img_prd,
                'total_prd' => $total,
                'id_prd' => $request->hiden_id_prd,
                'id_user' => $iduser,
            ];
            $this->Cartcheck->postCart($data);
            \Cart::clear();
            return redirect()->route('home.cart.sendmail');
        } else {
            return back()->with('error', "Bạn chưa có Sản Phẩm Nào Để Thanh Toán!");
        }
    }

    // Quan li don hang
    public function listCartAdmin()
    {
        $cartadmin = $this->Cartcheck->listCartAdmin();
        return view('admin.managercart', compact('cartadmin'));
    }

    public function verifyCart(Request $request)
    {
        $cartadmin = $this->Cartcheck->listCartAdmin();

        $getone = $this->Cartcheck->getOneCartAdmin($request->id);
        // dd($getone);
        return view('admin.editcheckout', compact('getone', 'cartadmin'));
    }

    public function postverifyCart(Request $request)
    {
        $data = [
            'status' => $request->status,
            'id' => $request->id,
        ];
        $getone = $this->Cartcheck->getOneCartAdmin($request->id);
        $mail = $getone->mail;
        // $mail = $getone[0]->mail;  // Một là biến ở trên hai là biến này
        // dd($getone);
        $arrr = [$getone];
        // dd($arrr);
        $quanity = $request->quantity;
        $nameuser = Auth::user()->name;
        if ($getone->status == 1) {
            return back()->with('success', "Bạn đã được xác thực nên không thể xác thực được nữa!");
        } else {
            $this->Cartcheck->postVerifyCartAdmin($data);
            Mail::send('client.email', compact('arrr', 'nameuser'), function ($message) use ($mail) {
                $message->from('hanhv2761@gmail.com', 'Van Hanh');
                $message->to($mail, $mail);
                $message->cc('levanhanh1718@gmail.com', 'Student Hanh');
                $message->subject('Chi tiết đơn hàng của bạn!');
            });
            dd($nameuser);
        }
        Cart::clear();
        return back()->with('success', "Bạn Đã Xác Thực Đơn Hàng Thành Công!");
    }
    public function deleteCartAdmin(Request $request)
    {
        // dd($request->id);
        $this->Cartcheck->DeleteCartAdmin($request->id);
        return back()->with('mess', "Bạn Đã Xóa Thành Công Đơn Hang!");
    }
}
