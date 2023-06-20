<?php

// Dekarasai Variabel yang berupa array kosong
// Membuat variabel yang berisi nilai default
$dataRutePendaftaran = [];
$pajak1 = 0;
$pajak2 = 0;
$totalPajak = 0;
// Variabel yang berisi path data json
$pathJson = "data/data.json";

// Array assosiatif yang berisi nama bandara beserta besar pajak
$listBandaraAsal = [
    "Soekarno-Hatta (CGK)"      => 50000,
    "Husein Sastranegara (BDO)"  => 30000,
    "Abdul Rachman Saleh (MLG)" => 40000,
    "Juanda (SUB)"              => 40000
];
$listBandaraTujuan = [
    "Ngurah Rai (DPS)"          => 80000,
    "Hasanuddin (UPG)"          => 70000,
    "Inanwatan (INX)"           => 90000,
    "Sultan Iskandarmuda (BTJ)" => 70000
];

// Mengambil file json 
$dataRuteJson = file_get_contents($pathJson);
// Mengubah data json ke array
$dataRutePendaftaran = json_decode($dataRuteJson, true);

// jika tombol submit ditekan 
if (isset($_POST['submit'])) {
    // Terima semua data dari form
    $maskapai = $_POST['maskapai'];
    $bandaraAsal = $_POST['bandaraAsal'];
    $bandaraTujuan = $_POST['bandaraTujuan'];
    $hargaTiket = $_POST['hargaTiket'];

    /** Fungsi yang menghitung total pajak 
     *  - argumen 1 array bandara
     *  - argumen 2 bandara dari form input
     */
    function cariPajak($listBandara, $bandara)
    {
        // melopoping list bandara 
        foreach ($listBandara as $namaBandara => $pajak) {
            // mencari nama bandara yang sama
            if ($bandara == $namaBandara) {
                // jika ada yang sama maka kembalikan pajaknya
                return  $pajak;
            }
        }
    }

    /** Fungsi yang menghitung total pajak 
     *  - argumen 1 array bandara asal
     *  - argumen 2 array bandara tujuan
     *  - argumen 3 bandara asal dari form input
     *  - argumen 4 bandara tujuan dari form input
     */
    function totalPajak($listBandaraAsal, $listBandaraTujuan, $bandaraAsal, $bandaraTujuan)
    {
        // gloabal variabel dara variabel pajak
        global $pajak1, $pajak2;
        // memasukkan nilai dari dungsi cari pajak
        $pajak1 = cariPajak($listBandaraAsal, $bandaraAsal);
        $pajak2 = cariPajak($listBandaraTujuan, $bandaraTujuan);
        // mengembalikan nilai dari hasi penjumlahan pajak
        return $pajak1 + $pajak2;
    }

    /** Fungsi yang menghitung penjumlahan harga tiket dengan total pajak
     *  - argumen 1 harga tiket dari form ipout
     *  - argumen 2 total pajak
     */
    function totalHargaTiketAkhir($hargaTiket, $totalPajak)
    {
        // mengembalikan nilai penjumlahan harga tiket dengan pajak
        return $totalPajak + $hargaTiket;
    }

    /** Fungsi yang memasukkan data ke data json dan mengembalikan nilai arrray terbaru
     *  - argumen 1 array data rute pendaftaran
     *  - argumen 2 total pajak
     *  - argumen 3 total harga tiket
     */
    function masukkanDataKeDatabase($dataRutePendaftaran, $pajak, $totalHargaTiket)
    {
        // variabel global
        global $pathJson, $maskapai, $bandaraAsal, $bandaraTujuan, $hargaTiket;

        // Menambahkan data array baru pada index terakhir 
        $dataRutePendaftaran[] = [
            $maskapai,
            $bandaraAsal,
            $bandaraTujuan,
            $hargaTiket,
            $pajak,
            $totalHargaTiket
        ];

        array_multisort($dataRutePendaftaran, SORT_ASC);

        // mengubah data array kebentuk string
        // dan memasukkan kedalam file data.json
        $dataDalamJson = json_encode($dataRutePendaftaran, JSON_PRETTY_PRINT);
        file_put_contents($pathJson, $dataDalamJson);

        // Mengembalikan array terbaru data rute pendaftaran
        return $dataRutePendaftaran;
    }

    // menghitung total pajak
    $totalPajak = totalPajak($listBandaraAsal, $listBandaraTujuan, $bandaraAsal, $bandaraTujuan);

    // menghitung total harga tiket akhir
    $totalHargaTiket = totalHargaTiketAkhir($hargaTiket, $totalPajak);

    // memasukkan data ke data json dan mengambil nilai array terbaru
    $dataRutePendaftaran = masukkanDataKeDatabase($dataRutePendaftaran, $totalPajak, $totalHargaTiket);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerbangan</title>
    <link rel="stylesheet" href="library/bootstrap/css/bootstrap.css">
</head>

<body>
    <div class="container">
        <div class="mt-5 mb-5 text-center">
            <img src="img/plane.png" alt="" class="img-fluid" style="width: 120px;">
            <h1 class="text-center">
                Pendaftaran Rute Penerbangan
            </h1>
        </div>
        <div class="content">
            <form action="" method="post">
                <div class="from-group row">
                    <label class="form-label col-3
                    " for="maskapai">
                        Maskapai
                    </label>
                    <input type="text" name="maskapai" id="maskapai" class="col form-control" required>
                </div>
                <div class="from-group mt-2 row">
                    <label for="bandaraAsal" class="form-label col-3">Bandara Asal</label>
                    <select class="form-select col" name="bandaraAsal" id="bandaraAsal">
                        <option value="Soekarno-Hatta (CGK)">Soekarno Hatta (CGK)</option>
                        <option value="Husein Sastranegara (BDO)">Husein Sastranegar (BDO)</option>
                        <option value="Abdul Rachman Saleh (MLG)">Abdul Rachman Saleh (MLG)</option>
                        <option value="Juanda (SUB)">Juanda (SUB)</option>
                    </select>
                </div>
                <div class="from-group mt-2 row">
                    <label for="bandaraTujuan" class="form-label col-3">Bandara Tujuan</label>
                    <select class="form-select col" name="bandaraTujuan" id="bandaraTujuan">
                        <option value="Ngurah Rai (DPS)">Ngurah Rai (DPS)</option>
                        <option value="Hasanuddin (UPG)">Hasanuddin (UPG)</option>
                        <option value="Inanwatan (INX)">Inanwatan (INX)</option>
                        <option value="Sultan Iskandarmuda (BTJ)">Sultan Iskandarmuda (BTJ)</option>
                    </select>
                </div>
                <div class="from-group row mt-2">
                    <label class="form-label col-3
                    " for="hargaTiket">
                        Harga Tiket
                    </label>
                    <input type="text" name="hargaTiket" id="hargaTiket" class="col form-control" required>
                </div>
                <div class="text-center mt-3 ">
                    <button type="submit" class="btn btn-success px-5" name="submit">
                        Submit
                    </button type="submit">
                </div>
            </form>
        </div>
        <div class="footer mt-5">
            <?php $i = 1;
            if ($dataRutePendaftaran == null) : ?>
                <?= "" ?>
            <?php else : ?>
                <?php if (count($dataRutePendaftaran) < 1) : ?>
                    <?= "" ?>
                <?php else : ?>
                    <table class="table table-striped table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Maskapai</th>
                                <th scope="col">Asal Penerbangan</th>
                                <th scope="col">Tujuan Penerbangan</th>
                                <th scope="col">Harga Tiket</th>
                                <th scope="col">Pajak</th>
                                <th scope="col">Total Harga Tiket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dataRutePendaftaran as $dataArray) : ?>
                                <tr>
                                    <th scope="row"><?= $i++ ?></th>
                                    <?php foreach ($dataArray as $data) : ?>
                                        <td><?= $data ?></td>
                                    <?php endforeach; ?>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>


    <script src="library/bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>