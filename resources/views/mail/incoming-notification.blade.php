<!DOCTYPE html>
<html>
<head>
    <title>Surat Masuk Baru</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <h2>Dear, {{  $record->mail_type === "incoming" ? $record->recipientContact->name : $record->senderContact->name }}</h2>

    <p>Anda telah {{ $record->mail_type === "incoming" ? 'menerima surat masuk baru' : 'mengirim surat' }}  yang telah diarsipkan ke dalam sistem.</p>

    <table cellpadding="5">
        <tr>
            <td><strong>Nomor Surat</strong></td>
            <td>: {{ $record->mail_number }}</td>
        </tr>
        <tr>
            <td><strong>{{ $record->mail_type === "incoming" ? 'Pengirim' : 'Penerima' }}</strong></td>
            <td>: {{ $record->mail_type === "incoming" ? $record->sender : $record->recipient }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Surat</strong></td>
            <td>: {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>Perihal</strong></td>
            <td>: {{ ucwords(strtolower($record->subject1)) }} {{ $record->subject2 ? '- '.ucwords(strtolower($record->subject2)) : '' }}</td>
        </tr>
    </table>

    <p>Terima kasih,<br>
    Mail Management Admin</p>

</body>
</html>