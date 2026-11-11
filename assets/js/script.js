document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.seat input:not(:disabled)').forEach(input => {
    input.addEventListener('change', () => input.closest('.seat').classList.toggle('selected', input.checked));
  });
  const form = document.getElementById('seatForm');
  if (form) form.addEventListener('submit', e => {
    if (!form.querySelector('input[name="seats[]"]:checked')) {
      e.preventDefault();
      alert('Лутфан ақаллан як ҷой интихоб кунед.');
    }
  });
});
