<?php
// index.php - Halaman publik customer (TANPA login, TANPA navigasi/menu admin)
include "config/koneksi.php";
include "include/spk_functions.php";

// ===== Kriteria rating bintang (index 0 = bintang 1 ... index 4 = bintang 5) =====
$KRITERIA_RATING = [
    'harga'       => ['Harga',            ['Sangat Mahal','Mahal','Cukup','Murah','Sangat Murah'],                  'fa-tag'],
    'rasa'        => ['Rasa',             ['Tidak Enak','Kurang Enak','Biasa','Enak','Sangat Enak'],                'fa-utensils'],
    'porsi'       => ['Porsi',            ['Sangat Sedikit','Sedikit','Cukup','Banyak','Sangat Banyak'],            'fa-box-open'],
    'popularitas' => ['Popularitas',      ['Tidak Dikenal','Kurang Populer','Biasa','Populer','Sangat Populer'],    'fa-fire'],
    'tekstur'     => ['Tekstur',          ['Sangat Kurang','Kurang','Cukup','Baik','Sangat Baik'],                  'fa-layer-group'],
    'variasi'     => ['Variasi Menu',     ['Tidak Variatif','Kurang Variatif','Cukup','Variatif','Sangat Variatif'],'fa-bars-staggered'],
    'kepraktisan' => ['Kepraktisan Menu', ['Sangat Ribet','Kurang Praktis','Cukup','Praktis','Sangat Praktis'],     'fa-bolt'],
    'value'       => ['Value for Money',  ['Tidak Sepadan','Kurang Sepadan','Cukup','Sepadan','Sangat Sepadan'],    'fa-gem'],
];

// ===== Simpan rating (dipanggil via fetch dari preview; tanpa login) =====
if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim_rating'){
    header('Content-Type: application/json; charset=utf-8');
    $out = function($ok, $extra = []){ echo json_encode(['ok' => $ok] + $extra, JSON_UNESCAPED_UNICODE); exit; };

    $menu_id = intval($_POST['menu_id'] ?? 0);
    $n = [];
    foreach(array_keys($KRITERIA_RATING) as $k){
        $v = intval($_POST['rating_'.$k] ?? 0);
        if($v < 1 || $v > 5) $out(false, ['msg' => 'Mohon beri bintang untuk semua 8 kriteria.']);
        $n[$k] = $v;
    }

    try {
        // Pastikan menu ada
        $cek = mysqli_prepare($conn, "SELECT id FROM daftar_menu WHERE id=?");
        mysqli_stmt_bind_param($cek, "i", $menu_id);
        mysqli_stmt_execute($cek);
        if(mysqli_num_rows(mysqli_stmt_get_result($cek)) === 0) $out(false, ['msg' => 'Menu tidak ditemukan.']);

        $rating_overall = (int) round(array_sum($n) / 8);
        $komentar = '';
        $username_sementara = 'customer_baru';

        mysqli_begin_transaction($conn);
        $ins = mysqli_prepare($conn, "INSERT INTO ulasan
            (menu_id, username, rating, rating_harga, rating_rasa, rating_porsi, rating_popularitas, rating_tekstur, rating_variasi, rating_kepraktisan, rating_value, komentar)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($ins, "isiiiiiiiiis",
            $menu_id, $username_sementara, $rating_overall,
            $n['harga'], $n['rasa'], $n['porsi'], $n['popularitas'],
            $n['tekstur'], $n['variasi'], $n['kepraktisan'], $n['value'],
            $komentar
        );
        mysqli_stmt_execute($ins);

        // username = customer_{id} mengikuti auto increment baris ulasan ini
        $new_id   = mysqli_insert_id($conn);
        $username = 'customer_' . $new_id;
        $upd = mysqli_prepare($conn, "UPDATE ulasan SET username=? WHERE id=?");
        mysqli_stmt_bind_param($upd, "si", $username, $new_id);
        mysqli_stmt_execute($upd);
        mysqli_commit($conn);

        $out(true, ['username' => $username]);
    } catch(Throwable $e){
        @mysqli_rollback($conn);
        $out(false, ['msg' => 'Gagal menyimpan penilaian. Pastikan tabel ulasan sudah diupdate.']);
    }
}

// Versi ringkas untuk JS
$KR_JS = [];
foreach($KRITERIA_RATING as $key => [$label, $opsi, $ikon]) $KR_JS[] = ['key'=>$key, 'label'=>$label, 'opsi'=>$opsi, 'ikon'=>$ikon];


$H = spk_hitung($conn);

// Urutan menu berdasarkan skor TOPSIS (sama dengan rekomendasi.php)
$ids = [];
if(!$H['error']){
    $ids = array_keys($H['alt']);
    usort($ids, fn($a,$b)=>$H['top_skor'][$b] <=> $H['top_skor'][$a]);
}

$kategori_list  = ['Semua','Gyoza','Gunkan','Sushi','Rice Mentai','Dimsum'];
$kategori_aktif = $_GET['kategori'] ?? 'Semua';
if(!in_array($kategori_aktif, $kategori_list, true)) $kategori_aktif = 'Semua';

// Top 3 selalu dari seluruh menu (tidak terpengaruh filter kategori)
$top3 = array_slice($ids, 0, 3);


// ===== Gambar & deskripsi dari tabel daftar_menu (sama seperti index.php) =====
$dm_by_id = []; $dm_by_nama = [];
$qdm = mysqli_query($conn, "SELECT id, nama_menu, gambar, deskripsi FROM daftar_menu");
if($qdm){
    while($r = mysqli_fetch_assoc($qdm)){
        $dm_by_id[(int)$r['id']] = $r;
        $dm_by_nama[strtolower(trim($r['nama_menu']))] = $r;
    }
}
function cust_row($id, $a){
    global $dm_by_id, $dm_by_nama;
    if(isset($dm_by_id[(int)$id])) return $dm_by_id[(int)$id];
    foreach(['nama','label'] as $f){
        if(!empty($a[$f])){
            $key = strtolower(trim($a[$f]));
            if(isset($dm_by_nama[$key])) return $dm_by_nama[$key];
        }
    }
    return [];
}
function cust_gambar($id, $a){
    $r = cust_row($id, $a);
    return !empty($r['gambar']) ? htmlspecialchars($r['gambar']) : '';
}
function cust_deskripsi($id, $a){
    $r = cust_row($id, $a);
    return $r['deskripsi'] ?? ($a['deskripsi'] ?? '');
}

// ===== Data untuk preview (dipakai modal; tanpa pindah halaman) =====
$preview = [];
if($ids){
    $kriteria_nama = [];
    $qk = mysqli_query($conn, "SELECT kode_kriteria, nama_kriteria FROM kriteria ORDER BY id ASC");
    while($k = mysqli_fetch_assoc($qk)) $kriteria_nama[$k['kode_kriteria']] = $k['nama_kriteria'];

    $kodes = array_values($H['kode']);
    $semua = [];
    foreach($ids as $id) foreach($H['X'][$id] as $v) $semua[] = (float)$v;
    $skala = (max($semua) <= 5) ? 5 : 10;   // skala rating untuk bar

    foreach($ids as $pos => $id){
        $a    = $H['alt'][$id];
        $vals = array_values($H['X'][$id]);
        $krit = [];
        foreach($kodes as $i => $kd){
            $krit[] = ['nama' => $kriteria_nama[$kd] ?? $kd, 'nilai' => (float)($vals[$i] ?? 0)];
        }
        $preview[$id] = [
            'menu_id'   => (int)(cust_row($id, $a)['id'] ?? 0) ?: null,
            'nama'      => $a['nama'],
            'kategori'  => $a['kategori'] ?? 'Umum',
            'harga'     => 'Rp ' . number_format($a['harga'], 0, ',', '.'),
            'rating'    => spk_fmt(array_sum($H['X'][$id]) / count($kodes), 2),
            'skor'      => spk_fmt($H['top_skor'][$id], 4),
            'rank'      => $pos + 1,
            'gambar'    => html_entity_decode(cust_gambar($id, $a), ENT_QUOTES),
            'deskripsi' => cust_deskripsi($id, $a),
            'krit'      => $krit,
            'skala'     => $skala,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Menu Raja Mentai</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--brand:#4477ce;--brand-dark:#315db0;--bg:#f4f6fb;--ink:#1f2937;--muted:#6b7280;}
*{box-sizing:border-box;}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--ink);margin:0;min-height:100vh;}

/* HEADER (brand saja, tanpa navigasi / login) */
.top{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.06);padding:12px clamp(14px,4vw,40px);}
.brand{display:flex;align-items:center;gap:12px;max-width:1280px;margin:0 auto;}
.brand img{width:48px;height:48px;object-fit:contain;}
.brand-name{font-size:clamp(17px,4.5vw,22px);font-weight:700;color:var(--brand);line-height:1.1;}
.brand-tag{font-size:clamp(11px,3vw,13px);color:var(--muted);}

.wrap{max-width:1280px;margin:0 auto;padding:clamp(14px,3vw,32px) clamp(14px,4vw,40px) 40px;}
.sec-title{font-weight:700;font-size:clamp(1rem,3.5vw,1.25rem);margin:0 0 12px;display:flex;align-items:center;gap:8px;}

/* Gambar & kartu */
.img{width:100%;aspect-ratio:4/3;object-fit:cover;background:#e8edf5;display:block;}
.img-ph{width:100%;aspect-ratio:4/3;background:#e8edf5;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:28px;}
.body-c{padding:12px 14px 14px;display:flex;flex-direction:column;gap:6px;flex-grow:1;}
.name{font-weight:700;font-size:clamp(13px,3.4vw,16px);line-height:1.3;}
.meta{display:flex;justify-content:space-between;align-items:center;gap:6px;flex-wrap:wrap;font-size:clamp(12px,3vw,13px);color:var(--muted);}
.price{font-weight:700;color:var(--brand);}
.kat{font-size:11px;font-weight:600;padding:3px 10px;border-radius:999px;background:#e0f2fe;color:#0369a1;width:fit-content;}
.btn-detail{margin-top:auto;display:block;text-align:center;padding:9px;min-height:40px;border-radius:10px;
    border:1px solid #d5dbe6;color:#374151;text-decoration:none;font-size:14px;font-weight:600;background:#fff;}
.btn-detail:hover{background:var(--brand);color:#fff;border-color:var(--brand);}
.card-x{position:relative;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 20px rgba(0,0,0,.07);display:flex;flex-direction:column;transition:transform .2s;}
.card-x:hover{transform:translateY(-4px);}

/* Rank badge */
.rank{position:absolute;top:10px;left:10px;z-index:2;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;background:#e2e8f0;color:#475569;box-shadow:0 2px 6px rgba(0,0,0,.15);}
.rank-1{background:#fef3c7;color:#d97706;}
.rank-2{background:#e2e8f0;color:#475569;}
.rank-3{background:#ffedd5;color:#ea580c;}

/* TOP 3: 3 kolom, di HP jadi carousel swipe */
.top3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:30px;}
.top3 .card-x.first{border:2px solid #f6c23e;}

/* Kategori: chip satu baris, bisa digeser, sticky */
.kat-bar{display:flex;gap:8px;overflow-x:auto;padding:8px 2px;margin-bottom:12px;position:sticky;top:0;z-index:20;
    background:var(--bg);scrollbar-width:none;-webkit-overflow-scrolling:touch;}
.kat-bar::-webkit-scrollbar{display:none;}
.chip{flex:0 0 auto;min-height:40px;display:inline-flex;align-items:center;padding:0 16px;border-radius:999px;
    background:#fff;border:1px solid #d5dbe6;color:#374151;text-decoration:none;font-weight:600;font-size:14px;white-space:nowrap;}
.chip.active{background:var(--brand);border-color:var(--brand);color:#fff;}

/* GRID MENU responsif: 2 kolom HP -> 3 -> 4 -> 5 otomatis */
.menu-grid{display:grid;gap:clamp(10px,2vw,20px);grid-template-columns:repeat(2,minmax(0,1fr));}
@media(min-width:600px){.menu-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
@media(min-width:900px){.menu-grid{grid-template-columns:repeat(4,minmax(0,1fr));}}
@media(min-width:1200px){.menu-grid{grid-template-columns:repeat(5,minmax(0,1fr));}}

@media(max-width:768px){
    .top3{display:flex;overflow-x:auto;gap:14px;margin:0 calc(-1 * clamp(14px,4vw,40px)) 24px;
        padding:4px clamp(14px,4vw,40px) 12px;scroll-snap-type:x mandatory;scrollbar-width:none;-webkit-overflow-scrolling:touch;}
    .top3::-webkit-scrollbar{display:none;}
    .top3 .card-x{flex:0 0 74%;max-width:300px;scroll-snap-align:center;}
    .card-x:hover{transform:none;}
    .menu-grid .body-c{padding:10px;}
}

.btn-detail{width:100%;cursor:pointer;font-family:inherit;}

/* PREVIEW: modal di desktop, full page di mobile (<768px) */
.pv-img{width:100%;max-height:300px;object-fit:cover;background:#e8edf5;display:block;border-radius:12px;}
.pv-ph{width:100%;height:200px;background:#e8edf5;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:48px;border-radius:12px;}
.pv-name{font-size:1.35rem;font-weight:700;margin:14px 0 6px;}
.pv-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:14px 0;}
.pv-stat{background:#f4f6fb;border-radius:12px;padding:10px;text-align:center;}
.pv-stat small{display:block;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px;}
.pv-stat b{font-size:1rem;}
.pv-krit{margin-top:6px;}
.pv-row{display:flex;align-items:center;gap:10px;margin-bottom:8px;font-size:13px;}
.pv-row span.l{flex:0 0 38%;color:#374151;}
.pv-bar{flex:1;height:8px;background:#e8edf5;border-radius:99px;overflow:hidden;}
.pv-bar i{display:block;height:100%;background:var(--brand);border-radius:99px;}
.pv-row span.v{flex:0 0 28px;text-align:right;font-weight:600;}
#pvModal .modal-content{border:0;border-radius:18px;}
@media(max-width:767.98px){#pvModal .modal-content{border-radius:0;}}

/* RATING BINTANG di preview */
.rate-grid{display:grid;grid-template-columns:1fr;gap:10px;}
@media(min-width:576px){.rate-grid{grid-template-columns:1fr 1fr;}}
.rate-item{background:#f8fafc;border:1px solid #e5eaf2;border-radius:14px;padding:10px 12px;}
.rate-head{font-size:13px;font-weight:700;color:#374151;margin-bottom:4px;display:flex;align-items:center;gap:7px;}
.stars{display:flex;gap:2px;}
.star{background:none;border:0;padding:2px;cursor:pointer;font-size:32px;line-height:1;color:#d6dde8;transition:transform .12s,color .12s;-webkit-tap-highlight-color:transparent;}
.star.on{color:#f59e0b;}
.star:hover{transform:scale(1.12);}
.rate-ends{display:flex;justify-content:space-between;gap:6px;font-size:10px;color:#9aa5b5;margin-top:2px;}
.rate-text{font-size:12px;font-weight:700;color:#9aa5b5;margin-top:4px;min-height:18px;}
.rate-text.filled{color:var(--brand);}
.rate-prog{font-size:12px;color:var(--muted);text-align:center;margin:12px 0 8px;}
.btn-submit{width:100%;min-height:46px;border:0;border-radius:12px;background:var(--brand);color:#fff;font-weight:700;font-size:15px;}
.btn-submit:hover:not(:disabled){background:var(--brand-dark);}
.btn-submit:disabled{opacity:.45;cursor:not-allowed;}
</style>
</head>
<body>

<header class="top">
    <div class="brand">
        <img src="images/logo.jpeg" alt="Raja Mentai">
        <div>
            <div class="brand-name">Raja Mentai</div>
            <div class="brand-tag">Nikmati menu mentai lezat dengan rekomendasi paling tepat.</div>
        </div>
    </div>
</header>

<main class="wrap">
<?php if($H['error']): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($H['error']); ?></div>
<?php else: ?>

<?php if($top3): ?>
<h2 class="sec-title"><i class="fa-solid fa-trophy text-warning"></i> Top 3 Rekomendasi</h2>
<div class="top3">
    <?php foreach($top3 as $i => $id):
        $a  = $H['alt'][$id];
        $rt = array_sum($H['X'][$id]) / count($H['kode']);
        $img = cust_gambar($id, $a); ?>
    <div class="card-x <?= $i==0 ? 'first' : ''; ?>">
        <span class="rank rank-<?= $i+1; ?>"><?= $i+1; ?></span>
        <?php if($img): ?><img class="img" src="<?= $img; ?>" alt="<?= htmlspecialchars($a['nama']); ?>">
        <?php else: ?><div class="img-ph"><i class="fa-solid fa-bowl-food"></i></div><?php endif; ?>
        <div class="body-c">
            <span class="kat"><?= htmlspecialchars($a['kategori'] ?? 'Umum'); ?></span>
            <div class="name"><?= htmlspecialchars($a['nama']); ?>
                <?php if($i==0): ?><i class="fa-solid fa-crown text-warning ms-1"></i><?php endif; ?></div>
            <div class="meta">
                <span class="price">Rp <?= number_format($a['harga'], 0, ',', '.'); ?></span>
                <span><i class="fa-solid fa-star text-warning"></i> <?= spk_fmt($rt, 2); ?></span>
            </div>
            <button type="button" class="btn-detail" data-id="<?= $id; ?>">Lihat Detail</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<h2 class="sec-title"><i class="fa-solid fa-utensils" style="color:var(--brand)"></i> Menu</h2>
<nav class="kat-bar" aria-label="Kategori">
    <?php foreach($kategori_list as $k): ?>
        <a class="chip <?= $kategori_aktif==$k ? 'active':''; ?>" href="index.php?kategori=<?= urlencode($k); ?>"><?= $k; ?></a>
    <?php endforeach; ?>
</nav>

<div class="menu-grid">
    <?php $n = 0;
    foreach($ids as $id):
        $a = $H['alt'][$id];
        if($kategori_aktif != 'Semua' && ($a['kategori'] ?? '') != $kategori_aktif) continue;
        $n++;
        $rt  = array_sum($H['X'][$id]) / count($H['kode']);
        $img = cust_gambar($id, $a); ?>
    <div class="card-x">
        <?php if($img): ?><img class="img" src="<?= $img; ?>" alt="<?= htmlspecialchars($a['nama']); ?>" loading="lazy">
        <?php else: ?><div class="img-ph"><i class="fa-solid fa-bowl-food"></i></div><?php endif; ?>
        <div class="body-c">
            <span class="kat"><?= htmlspecialchars($a['kategori'] ?? 'Umum'); ?></span>
            <div class="name"><?= htmlspecialchars($a['nama']); ?></div>
            <div class="meta">
                <span class="price">Rp <?= number_format($a['harga'], 0, ',', '.'); ?></span>
                <span><i class="fa-solid fa-star text-warning"></i> <?= spk_fmt($rt, 2); ?></span>
            </div>
            <button type="button" class="btn-detail" data-id="<?= $id; ?>">Detail</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php if($n == 0): ?>
    <p class="text-center text-muted py-4">Belum ada menu di kategori ini.</p>
<?php endif; ?>

<?php endif; ?>
</main>


<!-- PREVIEW MENU: modal di desktop, fullscreen di mobile -->
<div class="modal fade" id="pvModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down" style="max-width:560px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-utensils me-2" style="color:var(--brand)"></i>Detail Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="pvBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary w-100" style="background:var(--brand);border-color:var(--brand);min-height:44px" data-bs-dismiss="modal">Kembali ke Menu</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const PV = <?= json_encode($preview, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const KR = <?= json_encode($KR_JS, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const pvModal = new bootstrap.Modal(document.getElementById('pvModal'));
const pvBody  = document.getElementById('pvBody');
const esc = t => String(t ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

let perluReload = false;
document.getElementById('pvModal').addEventListener('hidden.bs.modal', () => { if(perluReload) location.reload(); });

function formRating(d){
    if(!d.menu_id) return `<div class="alert alert-secondary small mt-3 mb-0">Menu ini belum bisa dinilai.</div>`;
    const items = KR.map(k => `
        <div class="rate-item" data-key="${k.key}">
            <div class="rate-head"><i class="fa-solid ${k.ikon}" style="color:var(--brand)"></i> ${esc(k.label)}</div>
            <div class="stars" role="radiogroup" aria-label="${esc(k.label)}">
                ${k.opsi.map((t, i) => `<button type="button" class="star" data-v="${i+1}" aria-label="${i+1} bintang - ${esc(t)}"><i class="fa-solid fa-star"></i></button>`).join('')}
            </div>
            <div class="rate-ends"><span>1 = ${esc(k.opsi[0])}</span><span>5 = ${esc(k.opsi[4])}</span></div>
            <div class="rate-text">Belum dinilai</div>
            <input type="hidden" name="rating_${k.key}" value="">
        </div>`).join('');
    return `
        <div class="fw-bold mt-4 mb-1"><i class="fa-solid fa-star text-warning me-1"></i> Beri Penilaianmu</div>
        <div class="text-muted small mb-3">Ketuk bintang untuk tiap kriteria (1 = paling buruk, 5 = paling baik). Semua kriteria wajib diisi.</div>
        <form id="rateForm">
            <input type="hidden" name="aksi" value="kirim_rating">
            <input type="hidden" name="menu_id" value="${d.menu_id}">
            <div class="rate-grid">${items}</div>
            <div class="rate-prog" id="rateProg">0 dari ${KR.length} kriteria dinilai</div>
            <div id="rateMsg"></div>
            <button type="submit" class="btn-submit" id="rateBtn" disabled>Kirim Penilaian</button>
        </form>`;
}

function initRating(){
    const form = document.getElementById('rateForm');
    if(!form) return;
    const items = [...form.querySelectorAll('.rate-item')];
    const btn = document.getElementById('rateBtn'), prog = document.getElementById('rateProg'), msg = document.getElementById('rateMsg');

    const paint = (it, upto) => it.querySelectorAll('.star').forEach(s => s.classList.toggle('on', +s.dataset.v <= upto));
    const setText = (it, v) => {
        const k = KR.find(x => x.key === it.dataset.key), t = it.querySelector('.rate-text');
        if(v){ t.textContent = v + '/5 · ' + k.opsi[v-1]; t.classList.add('filled'); }
        else { t.textContent = 'Belum dinilai'; t.classList.remove('filled'); }
    };
    const update = () => {
        const done = items.filter(i => i.querySelector('input').value).length;
        prog.textContent = done + ' dari ' + items.length + ' kriteria dinilai';
        btn.disabled = done !== items.length;
    };

    items.forEach(it => {
        const input = it.querySelector('input');
        it.querySelectorAll('.star').forEach(s => {
            s.addEventListener('click', () => { input.value = s.dataset.v; paint(it, +s.dataset.v); setText(it, +s.dataset.v); update(); });
            s.addEventListener('mouseenter', () => { paint(it, +s.dataset.v); setText(it, +s.dataset.v); });
        });
        it.querySelector('.stars').addEventListener('mouseleave', () => { const v = +input.value || 0; paint(it, v); setText(it, v); });
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        btn.disabled = true; btn.textContent = 'Mengirim...'; msg.innerHTML = '';
        try {
            const res  = await fetch(location.pathname, { method:'POST', body:new FormData(form) });
            const data = await res.json();
            if(data.ok){
                perluReload = true; // muat ulang saat preview ditutup agar peringkat diperbarui
                form.outerHTML = `<div class="alert alert-success mt-2 mb-0"><i class="fa-solid fa-circle-check me-1"></i> Terima kasih! Penilaianmu berhasil dikirim (<b>${esc(data.username)}</b>).</div>`;
            } else {
                msg.innerHTML = `<div class="alert alert-danger small py-2">${esc(data.msg || 'Gagal mengirim penilaian.')}</div>`;
                btn.textContent = 'Kirim Penilaian'; update();
            }
        } catch(err){
            msg.innerHTML = `<div class="alert alert-danger small py-2">Koneksi bermasalah, coba lagi.</div>`;
            btn.textContent = 'Kirim Penilaian'; update();
        }
    });
}

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detail');
    if(!btn) return;
    const d = PV[btn.dataset.id];
    if(!d) return;

    const img = d.gambar
        ? `<img class="pv-img" src="${esc(d.gambar)}" alt="${esc(d.nama)}">`
        : `<div class="pv-ph"><i class="fa-solid fa-bowl-food"></i></div>`;
    const krit = d.krit.map(k => `
        <div class="pv-row">
            <span class="l">${esc(k.nama)}</span>
            <div class="pv-bar"><i style="width:${Math.min(100, k.nilai / d.skala * 100)}%"></i></div>
            <span class="v">${esc(k.nilai)}</span>
        </div>`).join('');

    pvBody.innerHTML = `
        ${img}
        <span class="kat mt-3 d-inline-block">${esc(d.kategori)}</span>
        <div class="pv-name">${esc(d.nama)}</div>
        ${d.deskripsi ? `<p class="text-muted mb-0">${esc(d.deskripsi)}</p>` : ''}
        <div class="pv-stats">
            <div class="pv-stat"><small>Harga</small><b class="price">${esc(d.harga)}</b></div>
            <div class="pv-stat"><small>Rating</small><b><i class="fa-solid fa-star text-warning"></i> ${esc(d.rating)}</b></div>
            <div class="pv-stat"><small>Peringkat</small><b>#${esc(d.rank)}</b></div>
        </div>
        ${formRating(d)}
        <hr class="my-4">
        <div class="text-muted small mb-1">Nilai preferensi: <b>${esc(d.skor)}</b></div>
        <div class="fw-bold mt-2 mb-2">Hasil Penilaian per Kriteria</div>
        <div class="pv-krit">${krit}</div>`;
    pvBody.parentElement.scrollTop = 0;
    pvModal.show();
    initRating();
});
</script>

</body>
</html>