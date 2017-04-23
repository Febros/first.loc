<?php
/**
 * Created by PhpStorm.
 * User: KT829A
 * Date: 24.02.2017
 * Time: 11:16
 */

namespace app\models;
use yii\db\ActiveRecord;

class Cart extends ActiveRecord
{
    /*Costa-rico(images-behavior module)*/
    public function behaviors()
    {
        return [
            'image' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }

    /*Добавление в карзину товаров*/
    public function addToCart($product, $qty = 1)
    {
            $mainImg = $product->getImage();
        if (isset($_SESSION['cart'][$product->id]))/*Если в массиве $_SESSION['cart'] есть текущий
        элемент product*/
        {
            $_SESSION['cart'][$product->id]['qty'] += $qty;/*Обращаемся к текущему элементу к свойству qty и добавим кол-во в qty если товар есть*/
        }
        else
        {
            $_SESSION['cart'][$product->id] = /*Если его нету то создаем со следующими элементами*/
                [
                  'qty' => $qty,
                  'name' => $product->name,
                  'price' => $product->price,
                  'img' => $mainImg->getUrl('x50'),
                ];
        }
        /*Если в корзине есть товар то прибавим qty(кол-во) иначе если нету то положим qty(1)*/
        $_SESSION['cart.qty'] = isset($_SESSION['cart.qty']) ? $_SESSION['cart.qty'] + $qty : $qty;
        /*Если сумма есть то берем его и прибавляем к нему кол-во умноженное на цену иначе просто
         *кладем количество умноженное на цену*/
        $_SESSION['cart.sum'] = isset($_SESSION['cart.sum']) ? $_SESSION['cart.sum'] + $qty * $product->price : $qty * $product->price;
    }

    /*Удаление товаров из корзины сначала проверка есть ли товар дальше идет пересчет итогового количества и суммы и потом удаляем товар*/
    public function recalc($id)
    {
        if (!isset($_SESSION['cart'][$id])) return false;/*Если в массиве $_SESSION['cart'] нету элемента product возвращаем false */
        $qtySub = $_SESSION['cart'][$id]['qty'];/*иначе в $qtySub помещаем количество товаров*/
        $sumSub = $_SESSION['cart'][$id]['qty'] * $_SESSION['cart'][$id]['price'];/*в $sumSub помещаем количество товаров которое удалемс
        умноженное на цену товара и получаем ту сумму которую нужно отнять из итоговой суммы */
        $_SESSION['cart.qty'] -= $qtySub; /*Отнимаем из итогового количества*/
        $_SESSION['cart.sum'] -= $sumSub; /*Отнимаем из итоговой суммы*/
        unset($_SESSION['cart'][$id]); /*Удаляем текущий товар*/
    }
}