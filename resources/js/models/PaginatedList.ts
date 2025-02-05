export default interface PaginatedList<T>
{
    data: T[],
    meta: {
        links: PageLink[],
    },
}

export interface PageLink
{
    label: string,
    url?: string,
    active: boolean,
}
