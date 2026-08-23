import { ref } from 'vue';

const dateFormat = ref('d/m/Y');
let promise = null;

const load = () => {
    if (promise) return promise;

    promise = fetch('https://api.zsubscriptions.local/defaults', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' },
    })
        .then(res => res.json())
        .then(data => {
            if (data.date_format) {
                dateFormat.value = data.date_format;
            }
        })
        .catch(() => {});

    return promise;
};

const parseParts = (value) => {
    const text = String(value);
    const match = text.match(/^(\d{4})-(\d{2})-(\d{2})/);

    if (match) {
        return {
            year: match[1],
            month: match[2],
            day: match[3],
        };
    }

    const date = new Date(text);
    if (isNaN(date.getTime())) return null;

    return {
        year: String(date.getUTCFullYear()),
        month: String(date.getUTCMonth() + 1).padStart(2, '0'),
        day: String(date.getUTCDate()).padStart(2, '0'),
    };
};

const formatDate = (value) => {
    if (! value) return '—';

    const parts = parseParts(value);
    if (! parts) return value;

    return dateFormat.value
        .replace(/Y/g, parts.year)
        .replace(/y/g, parts.year.slice(-2))
        .replace(/m/g, parts.month)
        .replace(/d/g, parts.day);
};

export default function useDateFormat() {
    load();
    return { dateFormat, formatDate };
}
