<?php declare(strict_types=1);

namespace NINJA\NicotineHint;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class NicotineHint extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $check=$customFieldSetRepository->search( (new Criteria())->addFilter(new EqualsFilter('name', 'custom_nicotine_hint')),$installContext->getContext());
        if($check->getTotal()==0) {
            $customFieldSetRepository->create([
                [
                    'name' => 'custom_nicotine_hint',
                    'config' => [
                        'label' => [
                            'de-DE' => 'Nikotin Hinweis anzeigen',
                            'en-GB' => 'Show Nicotine Hint'
                        ]
                    ],
                    'relations' => [[
                        'entityName' => 'product'
                    ]],
                    'customFields' => [
                        [
                            'name' => 'custom_nicotine_hint',
                            'type' => CustomFieldTypes::BOOL
                        ]
                    ]
                ]
            ], $installContext->getContext());
        }
    }
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $temp=$customFieldSetRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('name', 'custom_nicotine_hint'))
            ,Context::createDefaultContext());
        if($temp->getTotal()==1){
            $temp1=$temp->getEntities()->getElements();
            $id=array_shift($temp1)->getId();

            $customFieldSetRepository->delete([
                ['id'=>$id]
            ],$uninstallContext->getContext());
        }

    }
}
