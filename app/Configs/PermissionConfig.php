<?php

namespace App\Configs;

class PermissionConfig
{
    public static function getPermissions()
    {
        return [
            // Quan ly banner
            'banner.create',
            'banner.update',
            'banner.read',
            'banner.delete',
            // Quan ly danh muc
            'category.create',
            'category.read',
            'category.update',
            'category.delete',
            // Quan ly voucher
            'voucher.create',
            'voucher.read',
            'voucher.update',
            'voucher.delete',
            // Quan ly bai viet
            'post.create',
            'post.read',
            'post.update',
            'post.delete',
            // Quan ly quyen
            'permission.create',
            'permission.read',
            'permission.update',
            'permission.delete',
            // Quan ly nguoi dung
            'user.create',
            'user.read',
            'user.update',
            'user.delete',
            // Quan ly chung chi
            'certificate.create',
            'certificate.read',
            'certificate.update',
            'certificate.delete',
            // Quan ly cai dat he thong
            'system.read',
            // Quan ly doanh thu
            'revenue.read',
            // Quan ly giao dich
            'transaction.read',
            'transaction.update',
            // Kiem duyet khoa hoc
            'course.approve',
            // Kiem duyet giang vien
            'course.approve',
        ];
    }

    public static function isValid($permission)
    {
        return in_array($permission, self::getPermissions());
    }
}
