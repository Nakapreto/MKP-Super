<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class DateHelper {

    /**
     * Formato sql de data<BR />
     * Exemplo: 2013-10-28
     */
    CONST SQL_DATE_FORMAT = 'Y-m-d';

    /**
     * Formato brasileiro de data<BR />
     * Exemplo: 28/10/2013
     */
    CONST BR_DATE_FORMAT = 'd/m/Y';

    /**
     * Formato simplificado de data brasileiro<BR />
     * Exemplo: 28/10
     */
    CONST BR_DATE_SIMPLE_FORMAT = 'd/m';

    /**
     * Formato sql de hora<BR />
     * Exemplo: 15:12:59
     */
    CONST SQL_TIME_FORMAT = 'H:i:s';

    /**
     * Formato brasileiro de hora<BR />
     * Exemplo: 15:12:59
     */
    CONST BR_TIME_FORMAT = 'H:i:s';

    /**
     * Formato brasileiro simplificado de hora<BR />
     * Exemplo: 15:12
     */
    CONST BR_TIME_SIMPLE_FORMAT = 'H:i';

    /**
     * Formato sql de data e hora<BR />
     * Exemplo: 2013-10-28 15:12:59
     */
    CONST SQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Formato brasileiro de data e hora<BR />
     * Exemplo: 28/10/2013 15:12:59
     */
    CONST BR_DATETIME_FORMAT = 'd/m/Y H:i:s';

    /**
     * Formato brasileiro simplificado de data e hora<BR />
     * Exemplo: 28/10 15:12
     */
    CONST BR_DATETIME_SIMPLE_FORMAT = 'd/m H:i';



    public static function hoje($formatoRetorno = self::SQL_DATE_FORMAT)
    {
        $hoje = new DateTime();
        return $hoje->format($formatoRetorno);
    }

    /**
     * Retorna um timestampo com o horário atual
     * @param string $formatoRetorno formato a ser retornado
     * @return string horário atual no formato pedido
     */
    public static function agora($formatoRetorno = self::SQL_DATETIME_FORMAT)
    {
        return self::hoje($formatoRetorno);
    }

    /**
     * Converte uma data do formato SQL_DATE_FORMAT para o formato BR_DATE_FORMAT
     * Este método é um "Wrapper" para a função self::formataData.
     * Para conversões de data mais específicas, chame-a diretamente
     * @param $data String data a ser convertida
     */
    public static function sqlToBr($data)
    {
        return self::formataData($data, self::BR_DATE_FORMAT, self::SQL_DATE_FORMAT);
    }

    /**
     * Converte uma data do formato  BR_DATE_FORMAT para o formato SQL_DATE_FORMAT
     * Este método é um "Wrapper" para a função self::formataData.
     * Para conversões de data mais específicas, chame-a diretamente
     * @param $data String data a ser convertida
     */
    public static function brToSql($data)
    {
        return self::formataData($data, self::SQL_DATE_FORMAT, self::BR_DATE_FORMAT);
    }

    /**
     * Converte uma data do formato  BR_DATETIME_FORMAT para o formato SQL_DATETIME_FORMAT
     * Este método é um "Wrapper" para a função self::formataData.
     * Para conversões de data mais específicas, chame-a diretamente
     * @param $data String data a ser convertida
     */
    public static function dateTimeBrToSql($data)
    {
        return self::formataData($data, self::SQL_DATETIME_FORMAT, self::BR_DATETIME_FORMAT);
    }

    /**
     * Converte uma data do formato SQL_DATETIME_FORMAT para o formato BR_DATETIME_FORMAT
     * Este método é um "Wrapper" para a função self::formataData.
     * Para conversões de data mais específicas, chame-a diretamente
     * @param $data String data a ser convertida
     */
    public static function dateTimeSqlToBr($data)
    {
        return self::formataData($data, self::BR_DATETIME_FORMAT, self::SQL_DATETIME_FORMAT);
    }

    /**
     * @return data no padrão BR sem as horas do Timestamp
     */
    public static function dateTimeSqlToDateBr($data){
        return self::formataData($data,self::BR_DATE_FORMAT);
    }

    /**
     * @return data no padrão SQL sem as horas do Timestamp
    */
    public static function dateTimeSqlToDateSql($data){
        return self::formataData($data,self::SQL_DATE_FORMAT);
    }

    public static function formataData(
        $data,
        $formatoDestino = self::BR_DATETIME_FORMAT,
        $formatoOrigem = self::SQL_DATETIME_FORMAT
    ) {
        if ($data == null) {
            return null;
        }

        if (empty($data)) {
            return null;
        }

        $d = DateTime::createFromFormat($formatoOrigem, $data);
        if ($d != null) {
            return $d->format($formatoDestino);
        }
        return $data;
    }
}