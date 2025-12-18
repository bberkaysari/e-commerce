<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Product
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\EloquentProductRepository;

// Variant
use App\Repositories\Variant\VariantRepositoryInterface;
use App\Repositories\Variant\EloquentVariantRepository;

// Cart + CartItem
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Cart\EloquentCartRepository;
use App\Repositories\Cart\CartItemRepositoryInterface;
use App\Repositories\Cart\EloquentCartItemRepository;

// Order
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Order\EloquentOrderRepository;
use App\Repositories\Order\OrderItemRepositoryInterface;
use App\Repositories\Order\EloquentOrderItemRepository;
use App\Repositories\Order\OrderAddressRepositoryInterface;
use App\Repositories\Order\EloquentOrderAddressRepository;

// Import
use App\Repositories\Import\ImportBatchRepositoryInterface;
use App\Repositories\Import\EloquentImportBatchRepository;

// User
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\EloquentUserRepository;

// Payment
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\Payment\EloquentPaymentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);

        
        $this->app->bind(VariantRepositoryInterface::class, EloquentVariantRepository::class);

        
        $this->app->bind(CartRepositoryInterface::class, EloquentCartRepository::class);
        $this->app->bind(CartItemRepositoryInterface::class, EloquentCartItemRepository::class);

        
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, EloquentOrderItemRepository::class);
        $this->app->bind(OrderAddressRepositoryInterface::class, EloquentOrderAddressRepository::class);

       
        $this->app->bind(ImportBatchRepositoryInterface::class, EloquentImportBatchRepository::class);

        
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

       
        $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
    }

    public function boot(): void
    {
        
    }
}