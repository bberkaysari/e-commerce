<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Product
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;

// Variant
use App\Repositories\Interfaces\VariantRepositoryInterface;
use App\Repositories\Variant\VariantRepository;

// Cart + CartItem
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Cart\CartItemRepository;

// Order
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Order\OrderItemRepository;
use App\Repositories\Interfaces\OrderAddressRepositoryInterface;
use App\Repositories\Order\OrderAddressRepository;

// Import
use App\Repositories\Interfaces\ImportBatchRepositoryInterface;
use App\Repositories\Import\ImportBatchRepository;

// User
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\User\UserRepository;

// Payment
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Payment\PaymentRepository;

// Services
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Product\ProductService;
use App\Services\Interfaces\VariantServiceInterface;
use App\Services\Product\VariantService;
use App\Services\Interfaces\ImportServiceInterface;
use App\Services\Import\ImportProductsService;
use App\Services\Interfaces\PlaceOrderServiceInterface;
use App\Services\Order\PlaceOrderService;
use App\Services\Interfaces\CancelOrderServiceInterface;
use App\Services\Order\CancelOrderService;
use App\Services\Interfaces\RefundOrderServiceInterface;
use App\Services\Order\RefundOrderService;
use App\Services\Interfaces\OrderQueryServiceInterface;
use App\Services\Order\OrderQueryService;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Order\PaymentService;
use App\Services\Interfaces\CartServiceInterface;
use App\Services\Cart\CartService;
use App\Services\Interfaces\AddToCartServiceInterface;
use App\Services\Cart\AddToCartService;
use App\Services\Interfaces\UpdateCartItemServiceInterface;
use App\Services\Cart\UpdateCartItemService;
use App\Services\Interfaces\RemoveCartItemServiceInterface;
use App\Services\Cart\RemoveCartItemService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        
        $this->app->bind(VariantRepositoryInterface::class, VariantRepository::class);

        
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(CartItemRepositoryInterface::class, CartItemRepository::class);

        
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(OrderAddressRepositoryInterface::class, OrderAddressRepository::class);

       
        $this->app->bind(ImportBatchRepositoryInterface::class, ImportBatchRepository::class);

        
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

       
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(VariantServiceInterface::class, VariantService::class);
        $this->app->bind(ImportServiceInterface::class, ImportProductsService::class);
        $this->app->bind(PlaceOrderServiceInterface::class, PlaceOrderService::class);
        $this->app->bind(CancelOrderServiceInterface::class, CancelOrderService::class);
        $this->app->bind(RefundOrderServiceInterface::class, RefundOrderService::class);
        $this->app->bind(OrderQueryServiceInterface::class, OrderQueryService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(AddToCartServiceInterface::class, AddToCartService::class);
        $this->app->bind(UpdateCartItemServiceInterface::class, UpdateCartItemService::class);
        $this->app->bind(RemoveCartItemServiceInterface::class, RemoveCartItemService::class);
    }

    public function boot(): void
    {
        
    }
}
