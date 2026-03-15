<?php

namespace Tests\Feature\Inventory;

use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_create_inventory_category(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.inventory.categories.store'), [
                'code' => 'CAT-001',
                'name' => 'Electronics',
                'description' => 'Devices and accessories',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.inventory.categories.index'));
        $this->assertDatabaseHas('categories', ['code' => 'CAT-001']);
    }

    public function test_admin_can_create_supplier_and_product(): void
    {
        $category = Category::query()->create([
            'code' => 'CAT-002',
            'name' => 'Office Supplies',
            'is_active' => true,
        ]);

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-1001',
            'name' => 'Stationers Ltd',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.inventory.products.store'), [
                'sku' => 'PRD-3001',
                'name' => 'A4 Paper Box',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'unit_price' => 25000,
                'reorder_level' => 10,
                'stock_on_hand' => 40,
                'status' => 'active',
                'description' => '500 sheets x 5 reams',
            ]);

        $response->assertRedirect(route('admin.inventory.products.index'));
        $this->assertDatabaseHas('products', ['sku' => 'PRD-3001']);
    }

    public function test_admin_can_record_stock_movement(): void
    {
        $category = Category::query()->create([
            'code' => 'CAT-003',
            'name' => 'Spare Parts',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'sku' => 'PRD-4001',
            'name' => 'Filter Cartridge',
            'category_id' => $category->id,
            'unit_price' => 12000,
            'reorder_level' => 5,
            'stock_on_hand' => 20,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.inventory.stock.store'), [
                'product_id' => $product->id,
                'movement_type' => 'out',
                'quantity' => 3,
                'reference' => 'ISSUE-01',
                'status' => 'posted',
                'notes' => 'Issued to maintenance',
            ]);

        $response->assertRedirect(route('admin.inventory.stock.index'));
        $this->assertDatabaseHas('stock_movements', ['reference' => 'ISSUE-01']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_on_hand' => 17]);
    }

    public function test_non_admin_cannot_access_inventory_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $this->actingAs($user)
            ->get(route('admin.inventory.products.index'))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
