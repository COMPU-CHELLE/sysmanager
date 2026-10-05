import { useMemo, useState } from 'react';

export default function DataTable({
    columns,
    rows,
    rowKey = 'id',
    actions,
    searchable = true,
    emptyMessage = 'No hay registros.',
}) {
    const [search, setSearch] = useState('');

    const filteredRows = useMemo(() => {
        const term = search.trim().toLowerCase();

        if (!term) {
            return rows;
        }

        return rows.filter((row) =>
            columns.some(({ key, searchValue }) => {
                const value = searchValue ? searchValue(row) : row[key];

                return String(value ?? '').toLowerCase().includes(term);
            }),
        );
    }, [rows, columns, search]);

    return (
        <div>
            {searchable && (
                <div className="border-b border-slate-200 px-5 py-3 sm:px-6">
                    <input
                        type="search"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Buscar..."
                        aria-label="Buscar"
                        className="w-full rounded-md border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 sm:w-72"
                    />
                </div>
            )}

            {filteredRows.length === 0 ? (
                <p className="px-6 py-14 text-center text-sm text-slate-500">
                    {emptyMessage}
                </p>
            ) : (
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead className="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                {columns.map(({ key, label, className = '' }) => (
                                    <th key={key} className={`px-5 py-3 ${className}`}>
                                        {label}
                                    </th>
                                ))}
                                {actions && (
                                    <th className="px-5 py-3 text-right">Acciones</th>
                                )}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {filteredRows.map((row) => (
                                <tr key={row[rowKey]} className={row.deletedAt ? 'bg-slate-50 text-slate-400 line-through decoration-slate-300' : ''}>
                                    {columns.map(({ key, render, className = '' }) => (
                                        <td key={key} className={`px-5 py-4 ${className}`}>
                                            {render ? render(row) : row[key]}
                                        </td>
                                    ))}
                                    {actions && (
                                        <td className="px-5 py-4 text-right">
                                            {actions(row)}
                                        </td>
                                    )}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}
