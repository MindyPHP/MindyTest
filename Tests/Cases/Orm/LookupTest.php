<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 04/01/14.01.2014 00:53
 */

namespace Tests\Orm;


use Mindy\Orm\LookupBuilder;
use Tests\DatabaseTestCase;
use Tests\Models\Category;
use Tests\Models\Customer;
use Tests\Models\Order;
use Tests\Models\Product;
use Tests\Models\User;


class LookupTest extends DatabaseTestCase
{
    var $prefix = '';

    public function setUp()
    {
        parent::setUp();

        $this->initModels([
            new Order,
            new User,
            new Customer,
            new Product,
            new Category
        ]);

        $category = new Category;
        $category->name = 'test';
        $category->save();

        $user = new User;
        $user->password = 123456;
        $user->username = 'example';
        $user->save();

        $customer = new Customer;
        $customer->user = $user;
        $customer->address = 'example super address';
        $customer->save();

        $products = [];
        foreach([1, 2, 3, 4, 5] as $i) {
            $product = new Product;
            $product->name = $i;
            $product->price = $i;
            $product->description = $i;
            $product->category = $category;
            $product->save();
            $products[] = $product;
        }

        $order = new Order;
        $order->customer = $customer;
        $order->save();

        $order->products = $products;
        $order->save();

        $model = new Category();
        $this->prefix = $model->getConnection()->tablePrefix;
    }

    public function tearDown()
    {
        $this->dropModels([
            new Order,
            new User,
            new Customer,
            new Product,
            new Category
        ]);
    }

    public function testInit()
    {
        $this->assertEquals(5, Product::objects()->count());
        $this->assertEquals(1, Category::objects()->count());
        $this->assertEquals(1, User::objects()->count());
        $this->assertEquals(1, Customer::objects()->count());
        $this->assertEquals(1, Order::objects()->count());
        $this->assertEquals(5, Order::objects()->get(['pk' => 1])->products->count());
    }

    public function testExact()
    {
        $qs = Product::objects()->filter(['id' => 2]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals(1, $qs->count());
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id`=2)", $qs->countSql());
    }

    public function testIsNull()
    {
        $qs = Product::objects()->filter(['id__isnull' => true]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals(0, $qs->count());
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` IS NULL)", $qs->countSql());
    }

    public function testIn()
    {
        $qs = Product::objects()->filter(['category__in' => [1, 2, 3, 4, 5]]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`category` IN (1, 2, 3, 4, 5))", $qs->countSql());
    }

    public function testGte()
    {
        $qs = Product::objects()->filter(['id__gte' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`id` >= 1))", $qs->countSql());
    }

    public function testGt()
    {
        $qs = Product::objects()->filter(['id__gt' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`id` > 1))", $qs->countSql());
    }

    public function testLte()
    {
        $qs = Product::objects()->filter(['id__lte' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`id` <= 1))", $qs->countSql());
    }

    public function testLt()
    {
        $qs = Product::objects()->filter(['id__lt' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`id` < 1))", $qs->countSql());
    }

    public function testContains()
    {
        $qs = Product::objects()->filter(['id__contains' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` LIKE '%1%')", $qs->countSql());
    }

    public function testStartswith()
    {
        $qs = Product::objects()->filter(['id__startswith' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` LIKE '1%')", $qs->countSql());
    }

    public function testEndswith()
    {
        $qs = Product::objects()->filter(['id__endswith' => 1]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` LIKE '%1')", $qs->countSql());
    }

    public function testRange()
    {
        $qs = Product::objects()->filter(['id__range' => [0, 1]]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` BETWEEN 0 AND 1)", $qs->countSql());

        $qs = Product::objects()->filter(['id__range' => [10, 20]]);
        $this->assertInstanceOf('\Mindy\Orm\QuerySet', $qs);
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id` BETWEEN 10 AND 20)", $qs->countSql());
    }

    public function testSql()
    {
        $qs = Product::objects()
            ->filter(['name' => 'vasya', 'id__lte' => 7])
            ->filter(['name' => 'petya', 'id__gte' => 3]);

        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`name`='vasya') AND ((`id` <= 7))) AND ((`name`='petya') AND ((`id` >= 3)))", $qs->countSql());

        $qs = Product::objects()
            ->filter(['name' => 'vasya', 'id__lte' => 2])
            ->orFilter(['name' => 'petya', 'id__gte' => 4]);

        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE ((`name`='vasya') AND ((`id` <= 2))) OR ((`name`='petya') AND ((`id` >= 4)))", $qs->countSql());
    }

    public function testAllSql()
    {
        $qs = Product::objects()->filter(['id' => 1]);
        $this->assertEquals("SELECT * FROM `{$this->prefix}product` WHERE (`id`=1)", $qs->getSql());
        $this->assertEquals("SELECT * FROM `{$this->prefix}product` WHERE (`id`=1)", $qs->allSql());
        $this->assertEquals("SELECT COUNT(*) FROM `{$this->prefix}product` WHERE (`id`=1)", $qs->countSql());
    }
}
