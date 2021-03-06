<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addfks extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('rt_snippet', 'rt_snippet_site_id_rt_site_id', array(
             'name' => 'rt_snippet_site_id_rt_site_id',
             'local' => 'site_id',
             'foreign' => 'id',
             'foreignTable' => 'rt_site',
             'onUpdate' => NULL,
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('rt_snippet_version', 'rt_snippet_version_id_rt_snippet_id', array(
             'name' => 'rt_snippet_version_id_rt_snippet_id',
             'local' => 'id',
             'foreign' => 'id',
             'foreignTable' => 'rt_snippet',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
    }

    public function down()
    {
        $this->dropForeignKey('rt_snippet', 'rt_snippet_site_id_rt_site_id');
        $this->dropForeignKey('rt_snippet_version', 'rt_snippet_version_id_rt_snippet_id');
    }
}