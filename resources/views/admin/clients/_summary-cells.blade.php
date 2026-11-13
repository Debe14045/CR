<td class="py-3 px-4 text-center" style="white-space: nowrap;">
    <span class="badge rounded-pill fw-semibold" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.74rem; padding: 0.28rem 0.75rem;">
        {{ $client->pics->count() }} PIC
    </span>
</td>
<td class="py-3 px-4 text-center" style="white-space: nowrap;">
    <span class="badge rounded-pill fw-semibold" style="background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem; padding: 0.28rem 0.75rem;">
        {{ $client->projects->count() }} Proyek
    </span>
</td>
<td class="py-3 px-4 text-center" style="white-space: nowrap;">
    <span class="badge rounded-pill fw-semibold" style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-size: 0.74rem; padding: 0.28rem 0.75rem;">
        {{ $client->change_requests_count ?? 0 }} CR
    </span>
</td>

