<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user'; /*Таблица в БД*/
    }

    /*Возвращает найденного пользователя в БД по id*/
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /*Искать по токену(в данном проекте не требуется)*/
    public static function findIdentityByAccessToken($token, $type = null) {}

    /*Поиск пользователя по его логину */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /*Получить id*/
    public function getId()
    {
        return $this->id;
    }

    /*Получить данные из поля auth_key(из бд) авторизованного пользователя*/
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /*Сравнивает auth_key пользователя с тем что находится в базе*/
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /*Сравнивает пароль который ввел пользователь(преобразованый хеш-функцией getSecurity()->generatePasswordHash)
    * с паролем в базе(также хешированнным)
    * при этом класс security с методом validatePassword сравнивает хеши введенного пароля с паролем в базе
    */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /*Генерация случайного ключа авторизации для работы с Cookie*/
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
