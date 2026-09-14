<script>
    document.addEventListener('livewire:init', () => {
        const dirtyForms = new WeakSet();
        const disabledControls = new Map();

        document.addEventListener('input', (event) => {
            const form = event.target.closest('form');
            if (form) dirtyForms.add(form);
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form');
            if (form) dirtyForms.delete(form);
        }, true);

        window.addEventListener('beforeunload', (event) => {
            let hasDirtyForm = false;
            document.querySelectorAll('form').forEach((form) => {
                if (dirtyForms.has(form)) hasDirtyForm = true;
            });

            if (!hasDirtyForm) return;
            event.preventDefault();
            event.returnValue = '';
        });

        const setControlsDisabled = (disabled) => {
            if (disabled) {
                document.querySelectorAll('button, input[type="submit"]').forEach((control) => {
                    if (control.disabled) return;
                    disabledControls.set(control, true);
                    control.disabled = true;
                });
                return;
            }

            disabledControls.forEach((_, control) => {
                if (control.isConnected) control.disabled = false;
            });
            disabledControls.clear();
        };

        Livewire.hook('request', ({ succeed, fail }) => {
            setControlsDisabled(true);

            const finish = () => setControlsDisabled(false);
            succeed(finish);
            fail(finish);

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
