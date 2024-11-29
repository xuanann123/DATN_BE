import './bootstrap';
//window echo lấy trong boostrap
//Echo cái channel vouchers ra => muốn kích hoạt event thì dùng listen 
window.Echo.channel('vouchers')
    .listen('VoucherCreated', (event) => {
        console.log(event);
        // alert(`Voucher mới: ${event.name} giảm giá`);
    });