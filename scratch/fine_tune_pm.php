<?php

// 1. Update app.blade.php topbar user role and chevron
$appFile = 'resources/views/layouts/app.blade.php';
$appContent = file_get_contents($appFile);

// Replace user role line to display PM for PM
$searchUserRole = '<div class="text-muted text-truncate" style="font-size: 0.72rem; color: #64748B; text-transform: uppercase;" data-i18n="user_role_label">
                                {{ (auth()->check() && auth()->user()->isClient()) ? \'KLIEN\' : (auth()->check() ? strtoupper(auth()->user()->role) : \'KLIEN\') }}
                            </div>';
$replaceUserRole = '<div class="text-muted text-truncate" style="font-size: 0.72rem; color: #64748B; text-transform: uppercase;" data-i18n="user_role_label">
                                {{ (auth()->check() && auth()->user()->isClient()) ? \'KLIEN\' : \'PM\' }}
                            </div>';

$appContent = str_replace($searchUserRole, $replaceUserRole, $appContent);

// Remove chevron down from user dropdown trigger to match Figma exactly
$searchChevron = '<i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 0.75rem; font-weight: bold;"></i>';
// We can make it d-none or subtle
$replaceChevron = '';
$appContent = str_replace($searchChevron, $replaceChevron, $appContent);

file_put_contents($appFile, $appContent);
echo "Updated app.blade.php topbar!\n";

// 2. Refine SVG Bar Chart in cr/index.blade.php with wider bars (16px) and exact spacing
$crFile = 'resources/views/cr/index.blade.php';
$crContent = file_get_contents($crFile);

// Replace bar chart SVG block
$oldSvg = <<< 'HTML'
                    {{-- SVG Bar Chart matching Figma exactly --}}
                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 230" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{-- Horizontal Guide Lines & Y-Axis Labels matching Figma --}}
                            <line x1="45" y1="35" x2="525" y2="35" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="39" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">5</text>

                            <line x1="45" y1="65" x2="525" y2="65" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="69" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">4</text>

                            <line x1="45" y1="95" x2="525" y2="95" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="99" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">3</text>

                            <line x1="45" y1="135" x2="525" y2="135" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="139" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">1</text>

                            <line x1="45" y1="175" x2="525" y2="175" stroke="#CBD5E1" stroke-width="1.2" />
                            <text x="34" y="179" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">0</text>

                            {{-- Month 1: Jan (Masuk: 3, Keluar: 0, Batal: 0) --}}
                            <text x="78" y="90" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="73" y="95" width="11" height="80" rx="2" fill="#3B7CE7" />
                            <text x="91" y="170" font-size="9" font-weight="600" fill="#31A773" text-anchor="middle">0</text>
                            <rect x="86" y="173" width="11" height="2" rx="1" fill="#31A773" />
                            <text x="104" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="99" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="91" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Jan</text>

                            {{-- Month 2: Feb (Masuk: 4, Keluar: 1, Batal: 0) --}}
                            <text x="163" y="60" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="158" y="65" width="11" height="110" rx="2" fill="#3B7CE7" />
                            <text x="176" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="171" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="189" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="184" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="176" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Feb</text>

                            {{-- Month 3: Mar (Masuk: 1, Keluar: 1, Batal: 1) --}}
                            <text x="248" y="130" font-size="9.5" font-weight="600" fill="#3B7CE7" text-anchor="middle">1</text>
                            <rect x="243" y="135" width="11" height="40" rx="2" fill="#3B7CE7" />
                            <text x="261" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="256" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="274" y="130" font-size="9.5" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="269" y="135" width="11" height="40" rx="2" fill="#EF4444" />
                            <text x="261" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Mar</text>

                            {{-- Month 4: Apr (Masuk: 5, Keluar: 2, Batal: 0) --}}
                            <text x="333" y="30" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">5</text>
                            <rect x="328" y="35" width="11" height="140" rx="2" fill="#3B7CE7" />
                            <text x="346" y="105" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="341" y="110" width="11" height="65" rx="2" fill="#31A773" />
                            <text x="359" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="354" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="346" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Apr</text>

                            {{-- Month 5: Mei (Masuk: 3, Keluar: 1, Batal: 1) --}}
                            <text x="418" y="90" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="413" y="95" width="11" height="80" rx="2" fill="#3B7CE7" />
                            <text x="431" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="426" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="444" y="130" font-size="9.5" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="439" y="135" width="11" height="40" rx="2" fill="#EF4444" />
                            <text x="431" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Mei</text>

                            {{-- Month 6: Jun (Masuk: 4, Keluar: 2, Batal: 0) --}}
                            <text x="493" y="60" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="488" y="65" width="11" height="110" rx="2" fill="#3B7CE7" />
                            <text x="506" y="105" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="501" y="110" width="11" height="65" rx="2" fill="#31A773" />
                            <text x="519" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="514" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="506" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Jun</text>
                        </svg>
                    </div>
HTML;

$newSvg = <<< 'HTML'
                    {{-- SVG Bar Chart matching Figma exactly --}}
                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 230" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{-- Horizontal Guide Lines & Y-Axis Labels matching Figma --}}
                            <line x1="45" y1="35" x2="525" y2="35" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="39" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">5</text>

                            <line x1="45" y1="65" x2="525" y2="65" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="69" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">4</text>

                            <line x1="45" y1="95" x2="525" y2="95" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="99" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">3</text>

                            <line x1="45" y1="135" x2="525" y2="135" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="139" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">1</text>

                            <line x1="45" y1="175" x2="525" y2="175" stroke="#CBD5E1" stroke-width="1.2" />
                            <text x="34" y="179" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">0</text>

                            {{-- Month 1: Jan (Masuk: 3, Keluar: 0, Batal: 0) --}}
                            <text x="80" y="88" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="73" y="95" width="14" height="80" rx="2" fill="#3B7CE7" />
                            <text x="96" y="169" font-size="9" font-weight="600" fill="#31A773" text-anchor="middle">0</text>
                            <rect x="89" y="173" width="14" height="2" rx="1" fill="#31A773" />
                            <text x="112" y="169" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="105" y="173" width="14" height="2" rx="1" fill="#EF4444" />
                            <text x="96" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Jan</text>

                            {{-- Month 2: Feb (Masuk: 4, Keluar: 1, Batal: 0) --}}
                            <text x="165" y="58" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="158" y="65" width="14" height="110" rx="2" fill="#3B7CE7" />
                            <text x="181" y="128" font-size="10" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="174" y="135" width="14" height="40" rx="2" fill="#31A773" />
                            <text x="197" y="169" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="190" y="173" width="14" height="2" rx="1" fill="#EF4444" />
                            <text x="181" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Feb</text>

                            {{-- Month 3: Mar (Masuk: 1, Keluar: 1, Batal: 1) --}}
                            <text x="250" y="128" font-size="10" font-weight="600" fill="#3B7CE7" text-anchor="middle">1</text>
                            <rect x="243" y="135" width="14" height="40" rx="2" fill="#3B7CE7" />
                            <text x="266" y="128" font-size="10" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="259" y="135" width="14" height="40" rx="2" fill="#31A773" />
                            <text x="282" y="128" font-size="10" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="275" y="135" width="14" height="40" rx="2" fill="#EF4444" />
                            <text x="266" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Mar</text>

                            {{-- Month 4: Apr (Masuk: 5, Keluar: 2, Batal: 0) --}}
                            <text x="335" y="28" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">5</text>
                            <rect x="328" y="35" width="14" height="140" rx="2" fill="#3B7CE7" />
                            <text x="351" y="103" font-size="10" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="344" y="110" width="14" height="65" rx="2" fill="#31A773" />
                            <text x="367" y="169" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="360" y="173" width="14" height="2" rx="1" fill="#EF4444" />
                            <text x="351" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Apr</text>

                            {{-- Month 5: Mei (Masuk: 3, Keluar: 1, Batal: 1) --}}
                            <text x="420" y="88" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="413" y="95" width="14" height="80" rx="2" fill="#3B7CE7" />
                            <text x="436" y="128" font-size="10" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="429" y="135" width="14" height="40" rx="2" fill="#31A773" />
                            <text x="452" y="128" font-size="10" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="445" y="135" width="14" height="40" rx="2" fill="#EF4444" />
                            <text x="436" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Mei</text>

                            {{-- Month 6: Jun (Masuk: 4, Keluar: 2, Batal: 0) --}}
                            <text x="495" y="58" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="488" y="65" width="14" height="110" rx="2" fill="#3B7CE7" />
                            <text x="511" y="103" font-size="10" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="504" y="110" width="14" height="65" rx="2" fill="#31A773" />
                            <text x="527" y="169" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="520" y="173" width="14" height="2" rx="1" fill="#EF4444" />
                            <text x="511" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">Jun</text>
                        </svg>
                    </div>
HTML;

$oldSvgNorm = str_replace("\r\n", "\n", $oldSvg);
$newSvgNorm = str_replace("\r\n", "\n", $newSvg);
$crContentNorm = str_replace("\r\n", "\n", $crContent);

if (strpos($crContentNorm, $oldSvgNorm) !== false) {
    $crContentNorm = str_replace($oldSvgNorm, $newSvgNorm, $crContentNorm);
    file_put_contents($crFile, $crContentNorm);
    echo "Refined SVG bar chart successfully!\n";
} else {
    echo "Warning: oldSvg not matched\n";
}
