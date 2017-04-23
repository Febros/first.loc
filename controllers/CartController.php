<?php
namespace app\controllers;
use app\models\Product;
use app\models\Cart;
use app\models\Estimate;
use app\models\OrderItems;
use yii\helpers;
use Yii;
/**********************************************************
 *                  Корзина товаров                       *
***********************************************************/
class CartController extends AppController
{
    /*Вывод корзины по клику на иконеку корзины в header*/
    public function actionShow()
    {
        $session = Yii::$app->session;
        $session->open();
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    /*Добавление товаров в корзину*/
    public function actionAdd()
    {
        $id = Yii::$app->request->get('id');
        /*Получения от пользователся количества товаров ($qty)*/
        $qty = (int)Yii::$app->request->get('qty');
        /*Проверка на числовой параметр в qty от пользователя*/
        $qty = !$qty ? 1 : $qty; /*Если в qty не число(!= int) то присваеваем 1, инчаче оставляем то что ввел пользователь*/
        $product = Product::findOne($id);
        if (empty($product)) return false;
        $session = Yii::$app->session;/*Передача сессии в $session*/
        $session->open();/*Открытие сессии*/
        $cart = new Cart();/*Объект модели Cart*/
        $cart->addToCart($product, $qty);/*Вызов метода addToCart и передача $product*/
        /*Проверка Ajax запроса если нет то перенаправить пользвателя к предыдущей странице*/
        if (!Yii::$app->request->isAjax)
        {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $this->layout = false; /*отключение шаблона*/
        return $this->render('cart-modal', compact('session'));
    }

    /*Очистка корзины*/
    public function actionClear()
    {
        $session = Yii::$app->session;
        $session->open();
        $session->remove('cart');
        $session->remove('cart.qty');
        $session->remove('cart.sum');
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    /*Удаление товара из корзины*/
    public function actionDelItem()
    {
        $id = Yii::$app->request->get('id');
        $session = Yii::$app->session;
        $session->open();
        $cart = new Cart();
        $cart->recalc($id);
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }
    /*Оформление заказа*/
    public function actionView()
    {
        $session = Yii::$app->session;
        $session->open();
        $this->setMeta('Корзина');
        $estimate = new Estimate();
        if ($estimate->load(Yii::$app->request->post()) /*&& $estimate->validate()*/)/*Закгружаем данные введенные пользователем методом post*/
        {
            $estimate->qty = $session['cart.qty'];
            $estimate->sum = $session['cart.sum'];
            if ($estimate->save())
            {
                /*Отправка почты в локальную папку что бы отправить на реальный ящик в config/web
                 расскоментировать transport и поменять версию php на 5.6. на 7 не работает*/
                Yii::$app->mailer->compose('orderMail', compact('session'))
                ->setFrom(['user@email' => 'some message'])
                ->setTo($estimate->email)
                ->setSubject('Заказ')
                ->send();

                $this->saveOrderItems($session['cart'], $estimate->id);
                Yii::$app->session->setFlash('success', 'Ваш заказ принят. Менеджер с вами скоро свяжется');
                $session->remove('cart');
                $session->remove('cart.qty');
                $session->remove('cart.sum');
                return $this->refresh();
            }
            else
            {
                Yii::$app->session->setFlash('error', 'Ошибка оформления. Менеджер с вами скоро свяжется');
            }
        }
        return $this->render('view', compact('session', 'estimate'));
    }

    protected function saveOrderItems($items, $order_id)
    {
        foreach ($items as $id => $item)
        {
            $order_items = new OrderItems();
            $order_items->order_id = $order_id;
            $order_items->product_id = $id;
            $order_items->name = $item['name'];
            $order_items->price = $item['price'];
            $order_items->qty_item = $item['qty'];
            $order_items->sum_item = $item['qty'] * $item['price'];
            $order_items->save();
        }
    }
}