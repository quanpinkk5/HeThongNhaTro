<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết hợp đồng - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/chitiet_hopdong.css">
</head>
<body>
<div class="contract-container" id="contract-content">
    <div class="loading-state" style="text-align:center; padding:50px;">
        <i class="fas fa-spinner fa-spin"></i> Đang tải hợp đồng...
    </div>
</div>

<template id="contract-template">
    <div class="contract-paper">
        <header class="contract-header">
            <h1>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h1>
            <p class="slogan">Độc lập - Tự do - Hạnh phúc</p>
            <div class="line"></div>
            <h2 class="title">HỢP ĐỒNG THUÊ PHÒNG TRỌ</h2>
            <p class="contract-code"></p>
        </header>

        <section class="contract-section">
            <h3>I. BÊN CHO THUÊ (BÊN A)</h3>
            <div class="info-group">
                <p id="landlord_name"></p>
                <p id="landlord_phone"></p>
                <p id="landlord_email"></p>
            </div>
        </section>

        <section class="contract-section">
            <h3>II. BÊN THUÊ (BÊN B)</h3>
            <div class="info-group">
                <p id="tenant_name"></p>
                <p id="tenant_phone"></p>
                <p id="tenant_cccd"></p>
            </div>
        </section>

        <section class="contract-section">
            <h3>III. CHI TIẾT PHÒNG THUÊ</h3>
            <div class="info-grid">
                <p id="room_name"></p>
                <p id="room_area"></p>
                <p id="room_price"></p>
                <p id="room_deposit"></p>
                <p id="room_address" style="grid-column: span 2;"></p>
            </div>
        </section>

        <section class="contract-section">
            <h3>IV. THỜI HẠN THUÊ</h3>
            <p id="contract_time"></p>
        </section>

        <section class="contract-section">
            <h3>V. CÁC DỊCH VỤ ĐI KÈM</h3>
            <table class="service-table">
                <thead>
                    <tr>
                        <th>Tên dịch vụ</th>
                        <th>Đơn giá</th>
                        <th>Cách tính</th>
                    </tr>
                </thead>
                <tbody id="service-list"></tbody>
            </table>
        </section>

        <div class="contract-signature">
            <div class="sig-box">
                <p><strong>BÊN CHO THUÊ (BÊN A)</strong></p>
                <div class="sig-space"></div>
                <p id="sig_landlord"></p>
            </div>
            <div class="sig-box">
                <p><strong>BÊN THUÊ (BÊN B)</strong></p>
                <div class="sig-space"></div>
                <p id="sig_tenant"></p>
            </div>
        </div>
        
        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> In hợp đồng</button>
            <a href="phongdangthue.php" class="btn-back">Quay lại</a>
        </div>
    </div>
</template>

<script src="../../../public/js/customer/contract_detail.js"></script>
</body>
</html>