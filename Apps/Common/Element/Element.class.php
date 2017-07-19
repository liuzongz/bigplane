<?php
namespace Common\Element;

use Common\Element\Base\Di\Container;
use Common\Element\Base\Di\ServiceLocator;
use Common\Element\Favourable\Favourable;

class Element {

    public static function configure($object, $properties){
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

    /* @var ServiceLocator */
    public static $serviceLocator;

    /* @var Container */
    public static $container;

    public static function createObject($type, array $params=array()){
        if(is_string($type)) {
            return static::$container->get($type, $params);
        }elseif(is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return static::$container->get($class, $params, $type);
        //}elseif(is_callable($type, true)){
        //    return static::$container->invoke($type, $params);
        }elseif (is_array($type)) {
            E('类定义配置数组必须要有一个 "class" 元素');
        }else{
            E('未知的对象配置类型: '. gettype($type));
        }
    }










    //获取营销工具列表
    static public function getFavourables($instance=true, $all=false){
        if($instance){
            $favourables = self::getFavourables(false);
            foreach($favourables as $id => &$each){
                /* @var $each Favourable*/
                $each = self::$serviceLocator->get($id);
                if(!$all && $each->disabled){
                    unset($favourables[$id]);
                }
            }
            return $favourables;
        }else{
            //可以写在配置文件中
            $config = array(
                'discount'  => array('class' => 'Common\Element\Favourable\Discount',   'title' => '限时折扣'),
                'team'      => array('class' => 'Common\Element\Favourable\Team',       'title' => '团购'),
                'kp'        => array('class' => 'Common\Element\Favourable\Kp',         'title' => '送Kp点'),
                'integral'  => array('class' => 'Common\Element\Favourable\Integral',   'title' => '积分商品'),
                'panic'     => array('class' => 'Common\Element\Favourable\Panic',      'title' => '抢购'),
                'level'     => array('class' => 'Common\Element\Favourable\Level',      'title' => '三级分销')
            );
            foreach($config as $name => &$each)  $each['name'] = $name;
            return $config;
        }
    }

    //获取广告类型列表
    static public function getAdvTypes($instance=true){
        if($instance){
            $adv_types = self::getAdvTypes(false);
            foreach($adv_types as $id => &$each){
                $each = self::$serviceLocator->get($id);
            }
            return $adv_types;
        }else{
            //可以写在配置文件中
            $config = array(
                'img'   => array('class' => 'Common\Element\AdvType\Img',   'title' => '图片广告'),
                'text'  => array('class' => 'Common\Element\AdvType\Text',  'title' => '文字广告'),
            );
            foreach($config as $name => &$each)  $each['name'] = $name;
            return $config;
        }
    }
}

//底层基础组件
Element::$container        = new Container();
Element::$serviceLocator   = new ServiceLocator();

//自定义组件
Element::$container->setClasses(array(
    'price'     => array('class' => 'Common\Element\Price'),
    'activity'  => array('class' => 'Common\Element\Activity'),
    'goods'     => array('class' => 'Common\Element\Goods'),
    // ...
));

//自定义组件
Element::$serviceLocator->setElements(
    array_merge(
        Element::getFavourables(false),
        Element::getAdvTypes(false),
        array(

        // ...
    ))
);



