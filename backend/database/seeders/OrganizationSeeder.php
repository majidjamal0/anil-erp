<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\SalesChannel;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'ANIL'],
            ['name' => 'آنیل', 'default_locale' => 'fa', 'default_currency' => 'IRR', 'timezone' => 'Asia/Tehran', 'is_active' => true]
        );

        $branches = [];
        foreach ([
            ['GOLYAS', 'شعبه گل‌یاس', 'retail_branch', true, false],
            ['KOOHNOOR', 'شعبه کوه‌نور', 'retail_branch', true, false],
            ['CENTRAL-WORKSHOP', 'کارگاه مرکزی', 'workshop', true, false],
            ['PASDARAN', 'شعبه پاسداران', 'independent', false, true],
        ] as [$code, $name, $type, $operational, $external]) {
            $branches[$code] = Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                ['name' => $name, 'type' => $type, 'is_operational' => $operational, 'is_external' => $external, 'is_active' => true]
            );
        }

        $types = [];
        foreach ([
            ['SALES', 'فروش', true, true, false, false, true],
            ['RESERVE', 'رزرو', false, false, false, false, true],
            ['RAW_MATERIAL', 'مواد اولیه', false, false, true, false, false],
            ['WIP', 'در جریان تولید', false, false, false, true, false],
            ['FINISHED_GOODS', 'محصول نهایی', true, true, false, false, true],
            ['RETURNS', 'مرجوعی', false, false, false, false, true],
            ['DAMAGED', 'ضایعات', false, false, false, false, false],
            ['CONSIGNMENT', 'امانی', true, true, false, false, true],
            ['OTHER', 'سایر', false, false, false, false, false],
        ] as [$code, $name, $sellable, $shippable, $raw, $wip, $finished]) {
            $types[$code] = WarehouseType::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'is_sellable' => $sellable,
                    'is_shippable' => $shippable,
                    'supports_raw_materials' => $raw,
                    'supports_work_in_progress' => $wip,
                    'supports_finished_goods' => $finished,
                ]
            );
        }

        foreach ([
            ['GOLYAS-SALES', 'انبار فروشگاه گل‌یاس', 'GOLYAS', 'SALES'],
            ['GOLYAS-RESERVE', 'انبار پشتیبان گل‌یاس', 'GOLYAS', 'RESERVE'],
            ['GOLYAS-RETURNS', 'انبار مرجوعی گل‌یاس', 'GOLYAS', 'RETURNS'],
            ['KOOHNOOR-SALES', 'انبار فروشگاه کوه‌نور', 'KOOHNOOR', 'SALES'],
            ['KOOHNOOR-RESERVE', 'انبار پشتیبان کوه‌نور', 'KOOHNOOR', 'RESERVE'],
            ['FABRIC', 'انبار پارچه', 'CENTRAL-WORKSHOP', 'RAW_MATERIAL'],
            ['RAW', 'انبار مواد اولیه', 'CENTRAL-WORKSHOP', 'RAW_MATERIAL'],
            ['WIP', 'کالای در جریان تولید', 'CENTRAL-WORKSHOP', 'WIP'],
            ['FG', 'انبار محصول نهایی', 'CENTRAL-WORKSHOP', 'FINISHED_GOODS'],
            ['SCRAP', 'انبار ضایعات', 'CENTRAL-WORKSHOP', 'DAMAGED'],
        ] as [$code, $name, $branchCode, $typeCode]) {
            Warehouse::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'branch_id' => $branches[$branchCode]->id,
                    'warehouse_type_id' => $types[$typeCode]->id,
                    'is_sellable' => $types[$typeCode]->is_sellable,
                    'is_shippable' => $types[$typeCode]->is_shippable,
                ]
            );
        }

        foreach ([
            ['GOLYAS-POS', 'فروش حضوری گل‌یاس', 'physical_store', 'GOLYAS', false],
            ['KOOHNOOR-POS', 'فروش حضوری کوه‌نور', 'physical_store', 'KOOHNOOR', false],
            ['WEBSITE', 'سایت', 'website', null, true],
            ['INSTAGRAM', 'اینستاگرام', 'social', null, true],
            ['BALE', 'بله', 'social', null, true],
            ['EXHIBITION', 'نمایشگاه', 'exhibition', null, true],
            ['WHOLESALE', 'فروش عمده', 'wholesale', null, true],
            ['ORG', 'فروش سازمانی', 'organizational', null, true],
            ['CONSIGNMENT', 'فروش امانی', 'consignment', null, true],
        ] as [$code, $name, $type, $branchCode, $requiresWarehouseSelection]) {
            SalesChannel::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'type' => $type,
                    'branch_id' => $branchCode ? $branches[$branchCode]->id : null,
                    'requires_warehouse_selection' => $requiresWarehouseSelection,
                ]
            );
        }
    }
}
