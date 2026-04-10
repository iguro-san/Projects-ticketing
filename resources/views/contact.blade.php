@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-phone-alt"></i> Kontak Kami</h4>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-headset fa-4x text-primary mb-3"></i>
                        <h5>Customer Service</h5>
                        <p class="text-muted">Silakan hubungi kami untuk informasi lebih lanjut</p>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-phone"></i> Telepon</h6>
                                <p class="text-muted">0800-0000-0000</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-envelope"></i> Email</h6>
                                <p class="text-muted">support@eventmanagement.com</p>
                            </div>
                            <div class="col-12">
                                <h6><i class="fas fa-clock"></i> Jam Operasional</h6>
                                <p class="text-muted">Senin - Jumat: 09:00 - 17:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection