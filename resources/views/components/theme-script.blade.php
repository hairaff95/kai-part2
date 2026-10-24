<script>
    (function() {
        try {
            if (localStorage.getItem('kai_theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {}
    })();
</script>
