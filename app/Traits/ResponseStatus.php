<?php

namespace App\Traits;


trait ResponseStatus
{
    public function responsePOS($status, $message = NULL, $redirect = 'reload', $text_print = NULL): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Penjualan Berhasil Disimpan',
                'redirect' => $redirect,
                'text_print' => $text_print
            ];
        }
        return [
            'status' => 'error',
            'message' => $message ?? 'Login Gagal',
        ];
    }

    public function responseLogin($status, $message = NULL, $redirect = 'reload'): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Login Sukses',
                'redirect' => $redirect
            ];
        }
        return [
            'status' => 'error',
            'message' => $message ?? 'Login Gagal',
        ];
    }

    public function responseStore($status, $message = NULL, $redirect = 'reload'): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'redirect' => $redirect
            ];
        }
        return [
            'status' => 'error',
            'message' => $message ?? 'Data gagal dibuat',
        ];
    }

    public function responseUpdate($status, $redirect = 'reload'): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil diubah',
                'redirect' => $redirect
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Data gagal diubah'
        ];
    }

    public function responseDelete($status, $redirect = 'reload'): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'redirect' => $redirect
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ];
    }

    public function responseInstall($status, $message = NULL, $redirect = 'reload'): array
    {
        if ($status == true) {
            return [
                'status' => 'success',
                'message' => 'Erplite Berhasil Di Install',
                'redirect' => $redirect
            ];
        }
        return [
            'status' => 'error',
            'message' => $message ?? 'Erplite Gagal Di Install',
        ];
    }
}
