<?php

namespace App\Providers;

use Faker\Provider\Base as BaseProvider;

class ProductProvider extends BaseProvider {
    protected static $productNames = [
        'Laptop', 'Smartphone', 'Headphones', 'Backpack', 'Coffee Maker',
        'Bluetooth Speaker', 'Smartwatch', 'Tablet', 'Gaming Console', 'Camera',
        'Electric Toothbrush', 'Hair Dryer', 'Mixer', 'Vacuum Cleaner', 'Fitness Tracker',
        'Water Bottle', 'Wireless Charger', 'Desk Lamp', 'Portable Fan', 'Electric Kettle'
    ];

    protected static $productDescriptions = [
        'High-quality and durable', 'Innovative design', 'Compact and portable',
        'User-friendly interface', 'Energy-efficient', 'Sleek and stylish',
        'Versatile and multi-functional', 'Eco-friendly materials', 'Advanced technology',
        'Affordable and budget-friendly', 'Reliable performance', 'Easy to clean and maintain',
        'Lightweight and comfortable', 'Modern look', 'Ergonomically designed',
        'Perfect for everyday use', 'Convenient and practical', 'High-performance',
        'Suitable for all ages', 'Premium build quality'
    ];

    public function productName() {
        return static::randomElement(static::$productNames);
    }

    public function productDescription() {
        return static::randomElement(static::$productDescriptions);
    }
}
