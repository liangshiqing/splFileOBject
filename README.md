# myprojectname

####介绍：
####背景：简升对文件读写操作
>Reader.php
>>  功能：通过文件路径对文件进行迭代
>>  
>>  用法$reader = new CsvReader($url),foreach($reader->getIterator as $row)
>
----
>Writer.php
>>  功能：创建指定文件且进行数据的写入
>>  用法：$writer = new Writer($url,$header), $writer->writeln($fileds)
----
#####使用SplFileObject类操作大文件csv表格.对csv表格可进行快速处理,自己封装使用无高级技术
