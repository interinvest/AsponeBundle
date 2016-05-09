<?php

namespace InterInvest\AsponeBundle\Entity;

interface DeclarableTvaInterface extends DeclarableInterface
{
    public function get3310CA3CA();
    public function get3310CA3CG();
    public function get3310CA3GM();
    public function get3310CA3GH();
    public function get3310CA3HA();
    public function get3310CA3HB();
    public function get3310CA3HC();
    public function get3310CA3HD();
    public function get3310CA3JA();
    public function get3310CA3JB();
    public function get3310CA3JC();
    public function get3310CA3KA();
    public function get3310CA3KF();

    public function get3310CA3KE();

    /**
     * CA + CG
     * @return mixed
     */
    public function get3310CA3FM();

    /**
     * HA + HB + HC + HD
     * @return mixed
     */
    public function get3310CA3HG();

    /**
     * TVA npr
     * @return mixed
     */
    public function get3310CA3KG();

}