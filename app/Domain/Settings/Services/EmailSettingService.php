<?php

namespace App\Domain\Settings\Services;

use App\Domain\Companies\Services\CompanyContext;
use App\Models\EmailSetting;

class EmailSettingService
{
    public function __construct(
        protected CompanyContext $companyContext
    ) {}

    public function get(): EmailSetting
    {
        $company = $this->companyContext->get();

        return $company->emailSetting()->firstOrCreate(
            [
                'company_id' => $company->id,
            ],
            [
                'mail_mailer' => 'smtp',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
                'is_active' => false,
            ]
        );
    }

    public function update(array $data): EmailSetting
    {
        $setting = $this->get();

        /*
         * Password kosong berarti password lama
         * tidak diubah.
         */
        if (empty($data['mail_password'])) {
            unset($data['mail_password']);
        }

        $setting->update($data);

        return $setting->refresh();
    }
}
