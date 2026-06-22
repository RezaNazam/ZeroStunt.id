// ============================================================
// ZeroStunt.id — Main JavaScript
// Vanilla JS only. No jQuery. No frameworks.
// ============================================================

// Generic POST via Fetch API
async function postForm(url, formData) {
  try {
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
    });
    return await response.json();
  } catch (error) {
    console.error('Fetch error:', error);
    return { success: false, message: 'Network error. Please try again.' };
  }
}

// Show toast notification
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
  toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 transition-opacity`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 500);
  }, 3000);
}

// Show loading state on button
function setLoading(button, isLoading) {
  button.disabled = isLoading;
  button.textContent = isLoading ? 'Loading...' : button.dataset.originalText;
}

// Confirm before delete
function confirmDelete(url, message = 'Yakin ingin menghapus data ini?') {
  if (confirm(message)) {
    window.location.href = url;
  }
}

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', function () {
  const flashMessages = document.querySelectorAll('.flash-message');
  flashMessages.forEach(function (msg) {
    setTimeout(function () {
      msg.style.opacity = '0';
      setTimeout(function () {
        msg.remove();
      }, 500);
    }, 4000);
  });
});
