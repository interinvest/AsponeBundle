<?php

namespace InterInvest\AsponeBundle\Entity;

interface DeclarableTdfcInterface extends DeclarableInterface
{

    /**
     * @return "CRM" | "CVA" | "IAT" | "IDF" | "ILF" | "LOY" | "IPT"
     */
    public function getTypeDeclaration();

    public function getExercice();
    public function getIdentifBA();
    public function getIdentifBB();
    public function getIdentifBC();
    public function getIdentifCA();
    public function getIdentifCB();
    public function getIdentifDA();
    public function getIdentifDB();

    //formulaire 2033A
    public function do2033A();
    public function get2033AAC();
    public function get2033ABC();
    public function get2033ACC();
    public function get2033AAE();
    public function get2033ABE();
    public function get2033ACE();
    public function get2033AAJ();
    public function get2033ACJ();
    public function get2033AAK();
    public function get2033ACK();
    public function get2033AAQ();
    public function get2033ABQ();
    public function get2033ACQ();
    public function get2033AAR();
    public function get2033ABR();
    public function get2033ACR();
    public function get2033AFA();
    public function get2033AFF();
    public function get2033AFG();
    public function get2033AFH();
    public function get2033AFJ();
    public function get2033AFL();
    public function get2033AFM();
    public function get2033AFN();
    public function get2033AFP();
    public function get2033AFQ();
    public function get2033AFR();
    public function get2033AFS();
    public function get2033AJD();

    //formulaire 2033B
    public function do2033B();
    public function get2033BBC();
    public function get2033BBG();
    public function get2033BBH();
    public function get2033BBN();
    public function get2033BBP();
    public function get2033BBS();
    public function get2033BBT();
    public function get2033BBU();
    public function get2033BBV();
    public function get2033BBW();
    public function get2033BBX();
    public function get2033BBY();
    public function get2033BBZ();
    public function get2033BCA();
    public function get2033BCC();
    public function get2033BCD();
    public function get2033BED();
    public function get2033BCF();
    public function get2033BEL();
    public function get2033BCM();
    public function get2033BEM();
    public function get2033BEP();
    public function get2033BCR();
    public function get2033BER();
    public function get2033BJA();
    public function get2033BHU();
    public function get2033BHV();
    public function get2033BJB();

    //formulaire 2033C
    public function do2033C();
    public function get2033CAC();
    public function get2033CDC();
    public function get2033CAF();
    public function get2033CBF();
    public function get2033CCF();
    public function get2033CDF();
    public function get2033CAK();
    public function get2033CBK();
    public function get2033CCK();
    public function get2033CDK();
    public function get2033CFE();
    public function get2033CGE();
    public function get2033CHE();
    public function get2033CJE();
    public function get2033CFH();
    public function get2033CGH();
    public function get2033CHH();
    public function get2033CJH();
    public function get2033CRQ();

    //formulaire 2033D
    public function do2033D();
    public function get2033DPG();
    public function get2033DPH();
    public function get2033DPJ();
    public function get2033DMG();
    public function get2033DMH();
    public function get2033DPF();

    //formulaire 2033E
    public function do2033E();
    public function get2033EDB();

    //formulaire 2033F
    public function do2033F();
    public function get2033FGS();

    //formulaire 2033G
    public function do2033G();
    public function get2033GGS();

    //formulaire 2031
    public function do2031();
    public function get2031HA();
    public function get2031HB();
    public function get2031CW();
    public function get2031CX();
    public function get2031BG();

    //formulaire 2083
    public function do2083();
    public function get2083TE();
    public function get2083TF();
    public function getMultiple2083AA();
    public function getMultiple2083AB();
    public function getMultiple2083BA();
    public function getMultiple2083BB();
    public function getMultiple2083BC();
    public function getMultiple2083BW();
    public function getMultiple2083BX();
    public function getMultiple2083BD();
    public function getMultiple2083BE();
    public function getMultiple2083BF();
    public function getMultiple2083BG();
    public function getMultiple2083BH();
    public function getMultiple2083BJ();
    public function getMultiple2083BL();
    public function getMultiple2083BM();
    public function getMultiple2083BK();
    public function getMultiple2083BN();
    public function getMultiple2083BP();
    public function getMultiple2083BQ();
    public function getMultiple2083BS();
    public function getMultiple2083BR();
    public function getMultiple2083BT();
    public function getMultiple2083TA();
    public function getMultiple2083TB();
    public function getMultiple2083TC();
    public function getMultiple2083TD();
    public function getMultiple2083EK();
    public function getMultiple2083EA();
    public function getMultiple2083EB();
    public function getMultiple2083ED();
    public function getMultiple2083EE();
    public function getMultiple2083EF();
    public function getMultiple2083EG();
    public function getMultiple2083EH();
    public function getMultiple2083EJ();
    public function getMultiple2083ET();
    public function getMultiple2083EU();
    public function getMultiple2083EL();
    public function getMultiple2083EM();
    public function getMultiple2083EW();
    public function getMultiple2083EP();
    public function getMultiple2083PA();
    public function getMultiple2083PF();
    public function getMultiple2083PG();
}