<script>
    document.addEventListener('livewire:init', () => {
        const dirtyForms = new WeakSet();

        document.addEventListener('input', (event) => {
            const form = event.target.closest('form[data-unsaved-form]');
            if (form) dirtyForms.add(form);
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form[data-unsaved-form]');
            if (form) dirtyForms.delete(form);
        }, true);

        window.addEventListener('beforeunload', (event) => {
            let hasDirtyForm = false;
            document.querySelectorAll('form[data-unsaved-form]').forEach((form) => {
                if (dirtyForms.has(form)) hasDirtyForm = true;
            });

            if (!hasDirtyForm) return;
            event.preventDefault();
            event.returnValue = '';
        });

        Livewire.hook('request', ({ succeed, fail }) => {
            succeed(() => {
                document.querySelectorAll('form[data-unsaved-form]').forEach((form) => dirtyForms.delete(form));
            });

            fail(({ status, preventDefault }) => {
                if (status < 400) return;

                preventDefault();

                const error = document.getElementById('livewire-request-error');
                if (!error) return;

                const heading = error.querySelector('[data-livewire-error-heading]');
                const message = error.querySelector('[data-livewire-error-message]');

                if (status === 419) {
                    heading.textContent = 'Your session expired';
                    message.textContent = 'Refresh the page and submit the request again.';
                } else if (status === 403) {
                    heading.textContent = 'Access denied';
                    message.textContent = 'You no longer have permission to complete that action.';
                } else if (status === 404) {
                    heading.textContent = 'That item is no longer available';
                    message.textContent = 'Refresh the page to restore the current data.';
                } else {
                    heading.textContent = 'Something went wrong';
                    message.textContent = 'We could not complete that action. No changes were applied.';
                }

                error.classList.remove('hidden');
            });
        });
    });
</script>
