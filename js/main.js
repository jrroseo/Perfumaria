// Funções gerais do site
$(document).ready(function() {
    // Menu mobile
    $('.mobile-menu-toggle').click(function() {
        $('.main-nav').toggleClass('active');
    });
    
    // Dropdown do usuário
    $('.user-dropdown-toggle').click(function(e) {
        e.preventDefault();
        $(this).next('.user-dropdown').toggleClass('active');
    });
    
    // Fechar dropdown ao clicar fora
    $(document).click(function(e) {
        if (!$(e.target).closest('.user-dropdown-container').length) {
            $('.user-dropdown').removeClass('active');
        }
    });
    
    // Adicionar ao carrinho
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: 'ajax/add-to-cart.php',
            method: 'POST',
            data: { product_id: productId },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Atualizar contador do carrinho
                    $('.cart-count').text(data.cart_count);
                    
                    // Mostrar mensagem de sucesso
                    showNotification('Produto adicionado ao carrinho!', 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            }
        });
    });
});

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    const notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
    $('body').append(notification);
    
    notification.addClass('show');
    
    setTimeout(function() {
        notification.removeClass('show');
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 3000);
}