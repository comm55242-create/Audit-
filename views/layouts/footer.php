    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                setTimeout(() => {
                    loader.style.opacity = '0';
                    loader.style.visibility = 'hidden';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 400);
                }, 400); // 400ms delay to prevent white flashes while scripts boot up
            }
        });

        // Show loader again when navigating away to prevent white flash
        window.addEventListener('beforeunload', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.display = 'flex';
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
            }
        });
    </script>
</body>
</html>
