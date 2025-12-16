<!DOCTYPE html>
<html>
<head>
    <title>Surat Masuk Baru</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <h2>Dear, {{ $record->recipientContact->name ?? 'User' }}</h2>

    <p>Anda telah menerima surat masuk baru yang telah diarsipkan ke dalam sistem.</p>

    <table cellpadding="5">
        <tr>
            <td><strong>Nomor Surat</strong></td>
            <td>: {{ $record->mail_number }}</td>
        </tr>
        <tr>
            <td><strong>Pengirim</strong></td>
            <td>: {{ $record->sender }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Surat</strong></td>
            <td>: {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>Perihal</strong></td>
            <td>: {{ $record->subject1 }} {{ $record->subject2 ? '- '.$record->subject2 : '' }}</td>
        </tr>
    </table>

    <!-- <p>Silakan login ke aplikasi untuk melihat detail surat atau mengunduh lampiran.</p> -->

    <p>Terima kasih,<br>
    Admin Arsip</p>

</body>
</html>