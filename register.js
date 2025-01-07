document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // Prevent form from refreshing the page

    // Create a FormData object to gather form data
    const formData = new FormData(this);

    try {
        // Send a POST request to register.php
        const response = await fetch('register.php', {
            method: 'POST',
            body: formData,
        });

        // Parse the JSON response
        const result = await response.json();

        if (result.success) {
            alert(result.message); // Show success message
            window.location.href = 'login.php'; // Redirect to login page
        } else {
            alert(result.message); // Show error message
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});
