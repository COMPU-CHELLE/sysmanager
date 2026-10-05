import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function CrudModal({
    show,
    title,
    onClose,
    onSubmit,
    processing = false,
    submitLabel = 'Guardar',
    maxWidth = 'lg',
    children,
}) {
    const submit = (event) => {
        event.preventDefault();
        onSubmit();
    };

    return (
        <Modal show={show} onClose={onClose} maxWidth={maxWidth}>
            <form onSubmit={submit} className="p-6">
                <h2 className="text-lg font-semibold text-slate-950">{title}</h2>
                <div className="mt-5 space-y-5">{children}</div>
                <div className="mt-6 flex justify-end gap-3">
                    <SecondaryButton onClick={onClose}>Cancelar</SecondaryButton>
                    <PrimaryButton type="submit" disabled={processing}>
                        {submitLabel}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    );
}
