<?php include '../includes/header.php'; ?>
<div class="container mx-auto max-w-md py-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Create Account</h2>
    <form action="/app/auth.php?action=register" method="POST" class="bg-white p-8 rounded-lg shadow-md">
        <!-- Form fields: name, email, password -->
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Register
        </button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>