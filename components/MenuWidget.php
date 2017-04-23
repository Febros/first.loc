<?php

/**
 * Widget для работы с меню
 */

namespace app\components;
use yii\base\Widget;
use app\models\Category;
use Yii;


class MenuWidget extends Widget
{
    public $tpl;/*Сохраняется выбор пользователем шаблона(ul или select)*/
    public $model;/*Передается в селект для проверки категории если категория уже выбрана вводит selected*/
    public $data;/*Все записи категорий из БД*/
    public $tree;/*Результат работы функции, которая будет строить из обычного массива($data) массив дерева*/
    public $menuHtml;/*Готовый html-код в зависимости от того шаблона  который сохраниться в свойстве $tpl*/
    public $menu;
    public function init()
    {
        parent::init();
        if ($this->tpl === null)
        {
            $this->tpl = 'menu';
        }
        $this->tpl .= '.php'; /*Расширение php*/
    }

    public function run()
    {
        /*************************************************
        get Cache (получаем кэш) если кэш существует,
        то возвращаем кэш иначе достаем из БД
        **************************************************/
        if ($this->tpl == 'menu.php')/*Проверка для того чтобы меню кешировалось только в пользовательской части*/
        {
            $menu = Yii::$app->cache->get('menu');
            if ($menu) return $menu;
        }
        $this->data = Category::find()->indexBy('id')->asArray()->all();
        $this->tree = $this->getTree();
        $this->menuHtml = $this->getMenuHtml($this->tree);

        /*************************************************
        set Cache (Записываем кэш) если его нету
        'menu'- это ключь под которым записывается файл кэша
        $this->menuHtml- данные которые записываются в файл
        60- время(сек) на которое будет создаваться файл кэша
            т.е. после 1 минуты данные в файле кэша будут
            обновляться(из бд)
        cache хранится в папке runtime/cache/me
        **************************************************/
        if ($this->tpl == 'menu.php') /*Проверка для того чтобы меню кешировалось только в пользовательской части*/
        {
            Yii::$app->cache->set('menu', $this->menuHtml, 60);
        }
        return $this->menuHtml;
    }

    protected function getTree()
    {
        $tree = [];
        foreach ($this->data as $id=>&$node)
        {
            if (!$node['parent_id'])
                $tree[$id] = &$node;
            else
                $this->data[$node['parent_id']]['childs'][$node['id']] = &$node;
        }
        return $tree;
    }

    protected function getMenuHtml($tree, $tab = '')/*$tab параметр меню по умолчанию пустой символ если родельская категория,
                                                     иначе если потомок то рекурсивно получает в качестве параметра $tab ='->'*/
    {
        $str = '';
        foreach ($tree as $category)
        {
            $str .= $this->catToTemplate($category, $tab); /*Передача $tab параметром в функцию построения меню в панели администратора*/
        }
        return $str;
    }

    /*catToTemplate принимает параметр и передает его в шаблон(ob_get_clean)*/
    protected function catToTemplate($category, $tab) /*Принимаем параметр $tab для построения меню в панели администратора*/
    {
        ob_start();/*функция буферизации */
        include __DIR__ . '/menu_tpl/' . $this->tpl;
        return ob_get_clean();
    }
}