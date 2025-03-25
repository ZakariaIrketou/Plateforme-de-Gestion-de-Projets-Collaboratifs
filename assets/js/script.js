// Custom JavaScript for Projets Collaboratifs

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Smooth scrolling for anchor links
    $('a.smooth-scroll').click(function(event) {
        if (this.hash !== "") {
            event.preventDefault();
            var hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 800, function() {
                window.location.hash = hash;
            });
        }
    });
    
    // Add animation to cards on scroll
    $(window).scroll(function() {
        $('.card').each(function() {
            var bottom_of_object = $(this).offset().top + $(this).outerHeight();
            var bottom_of_window = $(window).scrollTop() + $(window).height();
            
            if (bottom_of_window > bottom_of_object) {
                $(this).addClass('animated fadeInUp');
            }
        });
    });
    
    // Form validation example
    $('#loginForm').submit(function(event) {
        var isValid = true;
        
        // Simple email validation
        var email = $('#email').val();
        if (!validateEmail(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }
        
        // Password validation
        var password = $('#password').val();
        if (password.length < 6) {
            $('#password').addClass('is-invalid');
            isValid = false;
        } else {
            $('#password').removeClass('is-invalid');
        }
        
        if (!isValid) {
            event.preventDefault();
        }
    });
    
    // Helper function for email validation
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Toggle password visibility
    $('.toggle-password').click(function() {
        var input = $($(this).attr('toggle'));
        if (input.attr('type') == 'password') {
            input.attr('type', 'text');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Project progress bar animation
    $('.progress-bar').each(function() {
        var width = $(this).data('width');
        $(this).css('width', '0%');
        $(this).animate({
            width: width + '%'
        }, 1000);
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages');
    const messageForm = document.getElementById('message-form');
    
    // If we have a messages container, load messages and set up polling
    if (messagesContainer) {
        const selectedUserId = messagesContainer.dataset.user;
        
        // Load messages initially
        loadMessages(selectedUserId);
        
        // Poll for new messages every 3 seconds
        setInterval(() => {
            loadMessages(selectedUserId);
        }, 3000);
        
        // Handle message form submission
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const receiverId = document.getElementById('receiver_id').value;
                const messageText = document.getElementById('message').value;
                
                if (messageText.trim() === '') return;
                
                sendMessage(receiverId, messageText);
            });
        }
    }
    
    // Function to load messages
    function loadMessages(userId) {
        fetch(`get_messages.php?user=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                displayMessages(data);
            })
            .catch(error => console.error('Error loading messages:', error));
    }
    
    // Function to display messages
    function displayMessages(messages) {
        if (!messagesContainer) return;
        
        let html = '';
        const currentUserId = document.body.dataset.userId || '<?php echo $_SESSION["user_id"]; ?>';
        
        messages.forEach(message => {
            const isMyMessage = message.sender_id === currentUserId;
            const messageClass = isMyMessage ? 'my-message' : 'other-message';
            
            html += `
                <div class="message ${messageClass}">
                    <div class="message-content">
                        <p>${message.message}</p>
                        <span class="message-time">${formatDate(message.created_at)}</span>
                    </div>
                </div>
            `;
        });
        
        messagesContainer.innerHTML = html;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Function to send a message
    function sendMessage(receiverId, message) {
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', message);
        
        fetch('send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            
            // Clear the message input
            document.getElementById('message').value = '';
            
            // Reload messages to show the new one
            loadMessages(receiverId);
        })
        .catch(error => console.error('Error sending message:', error));
    }
    
    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + 
               ' ' + date.toLocaleDateString();
    }
});