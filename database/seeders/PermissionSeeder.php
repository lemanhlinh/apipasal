<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = static::getDefaultPermission();

        foreach($permissions as $permission ) {
            Permission::updateOrCreate($permission);
        }

        $allPermissionNames = Permission::pluck('name')->toArray();

        $roleAdmin = Role::updateOrCreate([
            'name' => 'admin',
            'display_name' => 'Admin',
            'guard_name' => 'api',
        ]);

        $roleAdmin->givePermissionTo($allPermissionNames);
        $user = User::find(1);

        if ($user) {
            $user->assignRole($roleAdmin);
        }
    }

    public static function getDefaultPermission()
    {
        return [
            ['name' => 'view_user', 'display_name' => 'Xem danh sách người dùng', 'guard_name' => 'api'],
            ['name' => 'create_user', 'display_name' => 'Thêm mới người dùng', 'guard_name' => 'api'],
            ['name' => 'edit_user', 'display_name' => 'Sửa thông tin người dùng', 'guard_name' => 'api'],
            ['name' => 'delete_user', 'display_name' => 'Xóa người dùng', 'guard_name' => 'api'],

            ['name' => 'view_campuses', 'display_name' => 'Xem danh sách trung tâm', 'guard_name' => 'api'],
            ['name' => 'create_campuses', 'display_name' => 'Thêm mới trung tâm', 'guard_name' => 'api'],
            ['name' => 'edit_campuses', 'display_name' => 'Sửa trung tâm', 'guard_name' => 'api'],
            ['name' => 'delete_campuses', 'display_name' => 'Xóa trung tâm', 'guard_name' => 'api'],

            ['name' => 'view_departments', 'display_name' => 'Xem danh sách phòng ban', 'guard_name' => 'api'],
            ['name' => 'create_departments', 'display_name' => 'Thêm mới phòng ban', 'guard_name' => 'api'],
            ['name' => 'edit_departments', 'display_name' => 'Sửa phòng ban', 'guard_name' => 'api'],
            ['name' => 'delete_departments', 'display_name' => 'Xóa phòng ban', 'guard_name' => 'api'],

            ['name' => 'view_course_categories', 'display_name' => 'Xem danh sách nhóm khóa học', 'guard_name' => 'api'],
            ['name' => 'create_course_categories', 'display_name' => 'Thêm mới nhóm khóa học', 'guard_name' => 'api'],
            ['name' => 'edit_course_categories', 'display_name' => 'Sửa nhóm khóa học', 'guard_name' => 'api'],
            ['name' => 'delete_course_categories', 'display_name' => 'Xóa nhóm khóa học', 'guard_name' => 'api'],

            ['name' => 'view_courses', 'display_name' => 'Xem danh sách nhóm học', 'guard_name' => 'api'],
            ['name' => 'create_courses', 'display_name' => 'Thêm mới nhóm học', 'guard_name' => 'api'],
            ['name' => 'edit_courses', 'display_name' => 'Sửa nhóm học', 'guard_name' => 'api'],
            ['name' => 'delete_courses', 'display_name' => 'Xóa nhóm học', 'guard_name' => 'api'],

            ['name' => 'view_product_categories', 'display_name' => 'Xem danh sách nhóm sản phẩm', 'guard_name' => 'api'],
            ['name' => 'create_product_categories', 'display_name' => 'Thêm mới nhóm sản phẩm', 'guard_name' => 'api'],
            ['name' => 'edit_product_categories', 'display_name' => 'Sửa nhóm sản phẩm', 'guard_name' => 'api'],
            ['name' => 'delete_product_categories', 'display_name' => 'Xóa nhóm sản phẩm', 'guard_name' => 'api'],

            ['name' => 'view_products', 'display_name' => 'Xem danh sách sản phẩm', 'guard_name' => 'api'],
            ['name' => 'create_products', 'display_name' => 'Thêm mới sản phẩm', 'guard_name' => 'api'],
            ['name' => 'edit_products', 'display_name' => 'Sửa sản phẩm', 'guard_name' => 'api'],
            ['name' => 'delete_products', 'display_name' => 'Xóa sản phẩm', 'guard_name' => 'api'],

            ['name' => 'view_day_shift_learn', 'display_name' => 'Xem danh sách thời gian học', 'guard_name' => 'api'],
            ['name' => 'create_day_shift_learn', 'display_name' => 'Thêm mới thời gian học', 'guard_name' => 'api'],
            ['name' => 'edit_day_shift_learn', 'display_name' => 'Sửa thời gian học', 'guard_name' => 'api'],
            ['name' => 'delete_day_shift_learn', 'display_name' => 'Xóa thời gian học', 'guard_name' => 'api'],

            ['name' => 'view_calendar_learn', 'display_name' => 'Xem danh sách lịch học', 'guard_name' => 'api'],
            ['name' => 'create_calendar_learn', 'display_name' => 'Thêm mới lịch học', 'guard_name' => 'api'],
            ['name' => 'edit_calendar_learn', 'display_name' => 'Sửa lịch học', 'guard_name' => 'api'],
            ['name' => 'delete_calendar_learn', 'display_name' => 'Xóa lịch học', 'guard_name' => 'api'],

            ['name' => 'view_time_study', 'display_name' => 'Xem danh sách ca học', 'guard_name' => 'api'],
            ['name' => 'create_time_study', 'display_name' => 'Thêm mới ca học', 'guard_name' => 'api'],
            ['name' => 'edit_time_study', 'display_name' => 'Sửa ca học', 'guard_name' => 'api'],
            ['name' => 'delete_time_study', 'display_name' => 'Xóa ca học', 'guard_name' => 'api'],

            ['name' => 'view_regencies', 'display_name' => 'Xem danh sách chức vụ', 'guard_name' => 'api'],
            ['name' => 'create_regencies', 'display_name' => 'Thêm mới chức vụ', 'guard_name' => 'api'],
            ['name' => 'edit_regencies', 'display_name' => 'Sửa chức vụ', 'guard_name' => 'api'],
            ['name' => 'delete_regencies', 'display_name' => 'Xóa chức vụ', 'guard_name' => 'api'],

            ['name' => 'view_partners', 'display_name' => 'Xem danh sách đối tác', 'guard_name' => 'api'],
            ['name' => 'create_partners', 'display_name' => 'Thêm mới đối tác', 'guard_name' => 'api'],
            ['name' => 'edit_partners', 'display_name' => 'Sửa đối tác', 'guard_name' => 'api'],
            ['name' => 'delete_partners', 'display_name' => 'Xóa đối tác', 'guard_name' => 'api'],

            ['name' => 'view_markets', 'display_name' => 'Xem danh sách thị trường', 'guard_name' => 'api'],
            ['name' => 'create_markets', 'display_name' => 'Thêm mới thị trường', 'guard_name' => 'api'],
            ['name' => 'edit_markets', 'display_name' => 'Sửa thị trường', 'guard_name' => 'api'],
            ['name' => 'delete_markets', 'display_name' => 'Xóa thị trường', 'guard_name' => 'api'],

            ['name' => 'view_spendings', 'display_name' => 'Xem danh sách chi tiêu', 'guard_name' => 'api'],
            ['name' => 'create_spendings', 'display_name' => 'Thêm mới chi tiêu', 'guard_name' => 'api'],
            ['name' => 'edit_spendings', 'display_name' => 'Sửa chi tiêu', 'guard_name' => 'api'],
            ['name' => 'delete_spendings', 'display_name' => 'Xóa chi tiêu', 'guard_name' => 'api'],

            ['name' => 'view_report', 'display_name' => 'Xem danh sách báo cáo', 'guard_name' => 'api'],
            ['name' => 'create_report', 'display_name' => 'Thêm mới báo cáo', 'guard_name' => 'api'],
            ['name' => 'edit_report', 'display_name' => 'Sửa báo cáo', 'guard_name' => 'api'],
            ['name' => 'delete_report', 'display_name' => 'Xóa báo cáo', 'guard_name' => 'api'],

            ['name' => 'view_setting_customer', 'display_name' => 'Xem danh sách nguồn khách hàng', 'guard_name' => 'api'],
            ['name' => 'create_setting_customer', 'display_name' => 'Thêm mới nguồn khách hàng', 'guard_name' => 'api'],
            ['name' => 'edit_setting_customer', 'display_name' => 'Sửa nguồn khách hàng', 'guard_name' => 'api'],
            ['name' => 'delete_setting_customer', 'display_name' => 'Xóa nguồn khách hàng', 'guard_name' => 'api'],

            ['name' => 'view_setting_demo', 'display_name' => 'Xem danh sách loại demo trải nghiệm', 'guard_name' => 'api'],
            ['name' => 'create_setting_demo', 'display_name' => 'Thêm mới loại demo trải nghiệm', 'guard_name' => 'api'],
            ['name' => 'edit_setting_demo', 'display_name' => 'Sửa loại demo trải nghiệm', 'guard_name' => 'api'],
            ['name' => 'delete_setting_demo', 'display_name' => 'Xóa loại demo trải nghiệm', 'guard_name' => 'api'],

            ['name' => 'view_setting_ware', 'display_name' => 'Xem danh sách ngày vào kho', 'guard_name' => 'api'],
            ['name' => 'create_setting_ware', 'display_name' => 'Thêm mới ngày vào kho', 'guard_name' => 'api'],
            ['name' => 'edit_setting_ware', 'display_name' => 'Sửa ngày vào kho', 'guard_name' => 'api'],
            ['name' => 'delete_setting_ware', 'display_name' => 'Xóa ngày vào kho', 'guard_name' => 'api'],
        ];
    }
}
