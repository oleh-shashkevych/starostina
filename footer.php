<?php wp_footer(); ?>
    
    <script>
        // Простой JS для скролла, перенесли из HTML
        function scrollToPayment() {
            const paymentSection = document.getElementById('payment-section');
            if(paymentSection) {
                paymentSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>