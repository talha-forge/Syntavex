// The server sends UTC timestamps; these render them in the visitor's own time zone.

const pad = (value: number, length = 2): string => String(value).padStart(length, '0');

export const localZone = (at: Date = new Date()): string =>
    new Intl.DateTimeFormat(undefined, { timeZoneName: 'short' })
        .formatToParts(at)
        .find((part) => part.type === 'timeZoneName')?.value ?? '';

export const localClock = (iso: string, withMs = false): string => {
    const at = new Date(iso);
    const clock = `${pad(at.getHours())}:${pad(at.getMinutes())}:${pad(at.getSeconds())}`;

    return withMs ? `${clock}.${pad(at.getMilliseconds(), 3)}` : clock;
};

export const localShortDate = (iso: string): string =>
    new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });

export const localDateTime = (iso: string): string => {
    const at = new Date(iso);
    const date = `${at.getFullYear()}-${pad(at.getMonth() + 1)}-${pad(at.getDate())}`;

    return `${date} ${localClock(iso)} ${localZone(at)}`.trim();
};
