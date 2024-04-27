<?php

namespace App\Models;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Drive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDocs extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'phone',
        'text',
        'sheetUrl'
    ];

    public function getClient($scope = Sheets::SPREADSHEETS)
    {
        $client = new Client();
        $client->setApplicationName('Google Docs API');
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setScopes($scope);

        return $client;
    }

    public function createSheet($id, $values)
    {
        $client = $this->getClient();
        $clientDrive = $this->getClient(Drive::DRIVE);
        $sheets = new Sheets($client);
        
        $spreadsheet = new Sheets\Spreadsheet([
            'properties' => [
                'title' => "Sheet #$id",
            ]
        ]);
        
        $newSheet = $sheets->spreadsheets->create($spreadsheet);

        $permission = new Drive\Permission([
            'role' => 'reader',
            'type' => 'anyone'
        ]);

        $drive = new Drive($clientDrive);
        $drive->permissions->create($newSheet->spreadsheetId, $permission);

        $this->appendRow($newSheet->spreadsheetId, ['First name', 'Last name', 'Phone', 'Text']);
        $this->appendRow($newSheet->spreadsheetId, $values);
        return $newSheet->spreadsheetUrl;
    }

    public function appendRow($spreadsheetId, $values)
    {
        $client = $this->getClient();
        $service = new Sheets($client);

        $opt = ['valueInputOption' => 'RAW'];
        $range = new Sheets\ValueRange([
            'values' => [
                $values
            ]
        ]);

        $count = $service->spreadsheets_values->get($spreadsheetId, 'A:D')->count() + 1;

        $service->spreadsheets_values->append($spreadsheetId, "A$count:D$count", $range, $opt);
    }
}
