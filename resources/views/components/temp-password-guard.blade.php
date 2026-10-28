{{-- Komponen ini menampilkan banner countdown dan auto-logout jika user login via temp password --}}
@auth
@if(session('is_using_temp_password') && session('temp_password_expires_at'))
<script>
(function () {
    const expiresAt = {{ session('temp_password_expires_at') }};
    const logoutUrl = '{{ route("logout") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Banner countdown
    const banner = document.createElement('div');
    banner.id = 'temp-pwd-banner';
    banner.style.cssText = [
        'position:fixed','bottom:0','left:0','right:0','z-index:9999',
        'background:#F37021','color:#fff','font-family:inherit',
        'padding:10px 20px','display:flex','align-items:center',
        'justify-content:center','gap:10px','font-size:13px',
        'font-weight:600','box-shadow:0 -2px 12px rgba(0,0,0,0.2)',
    ].join(';');
    banner.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>Anda login menggunakan <strong>password sementara</strong>. Sesi berakhir dalam
            <strong id="tmp-countdown">--:--</strong>
            &mdash; Segera ganti kata sandi atau tunggu sistem memperbarui sesi Anda.
        </span>`;
    document.body.appendChild(banner);

    // Toast expired
    function showExpiredToast() {
        const toast = document.createElement('div');
        toast.style.cssText = [
            'position:fixed','top:24px','right:24px','z-index:99999',
            'background:#1F2123','color:#fff','border:1px solid rgba(255,255,255,0.1)',
            'border-radius:14px','padding:14px 18px','display:flex',
            'align-items:flex-start','gap:12px','max-width:340px',
            'box-shadow:0 8px 32px rgba(0,0,0,0.4)','font-size:13px','line-height:1.5',
        ].join(';');
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                fill="none" stroke="#EF4444" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round"
                style="flex-shrink:0;margin-top:1px">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <div style="font-weight:700;color:#EF4444;margin-bottom:3px">Password Sementara Kedaluwarsa</div>
                <div style="color:#9CA3AF">Sesi Anda telah berakhir. Logout otomatis...</div>
            </div>`;
        document.body.appendChild(toast);
    }

    // Logout via form POST
    function doLogout() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = logoutUrl;
        form.style.display = 'none';
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    // Countdown
    const countdownEl = document.getElementById('tmp-countdown');

    function tick() {
        const secondsLeft = expiresAt - Math.floor(Date.now() / 1000);
        if (secondsLeft <= 0) {
            if (countdownEl) countdownEl.textContent = '00:00';
            clearInterval(timer);
            showExpiredToast();
            setTimeout(doLogout, 2500);
            return;
        }
        const m = String(Math.floor(secondsLeft / 60)).padStart(2, '0');
        const s = String(secondsLeft % 60).padStart(2, '0');
        if (countdownEl) countdownEl.textContent = `${m}:${s}`;
        if (secondsLeft <= 60) banner.style.background = '#EF4444';
    }

    tick();
    const timer = setInterval(tick, 1000);
})();
</script>
@endif
@endauth