<?php

namespace AppBundle\Form;

use AppBundle\Entity\Server;
use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QueueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Server $server */
        $server = $options['server'];

        $uri = 'http://' . $server->getHost() . ':' . $server->getPort() . '/api/vhosts/';

        $client = new Client(
            [
                'base_uri' => $uri
            ]
        );

        $options = [
            'auth' => [
                $server->getUser(),
                $server->getPass()
            ]
        ];

        $resp = $client->get('', $options);
        $data = json_decode($resp->getBody()->getContents(), true);

        $names = [];

        foreach ($data as $vhostInfo) {
            if ($vhostInfo['name'] == '/') {
                $names[$vhostInfo['name']] = '_';
            } else {
                $names[$vhostInfo['name']] = $vhostInfo['name'];
            }
        }

        $builder->add('name')->add('vhost', ChoiceType::class, array(
            'choices'  => $names
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Queue',
            'server' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_queue';
    }


}
