package main

import (
    "bufio"
    "fmt"
    "math/rand"
    "os" 
)

func main() {
    fileSize := int64(3e9) // 3gb
    f, err := os.Create("/home/odelibalta/Documents/PMG/dataHuge/household_cleaners_huge.csv")
    if err != nil {
        fmt.Println(err)
        return
    }
    w := bufio.NewWriter(f)
    categories := []string{"Kitchen Cleaner", "Bathroom Cleaner" }
    hashstrs   := []string{ "b9f6f22276c919da793da65c76345ebb0b072257d12402107d09c89bc369a6b6",
                            "c2b5fa9e09ef2464a2b9ed7e351a5e1499823083c057913c6995fdf4335c73e7",
                            "faaee0ff7d06b05313ecb75d46a9aed014b11023ca1af5ec21a0607848071d18",
                            "5cd72da5035f2b36b604a16efc639cd04b6cfae7e487dcba60db88d3ef870f1e",
                            "00449a86bb34e443a5f84635661f63514574a030a15e2e57b81b2c3f4fa49650",
                            "b6161d458c4cc28331291235d05b3e458918d2ed21abfc4b86e4bac047b5553c",
                            "e088d5dbbca4c61e870dd32e2584623da88870327f060bd0d27465136366f353",
                            "4727745894cc3d99632c89a64067f69327913989cddb9e2de5793d4de12ef4ef",
                            "1ed6083eb9dc193dcd42466b57adce9f581cd70207426a37036a5dda3b3ad55a" }

                              
    size := int64(0)
    for size < fileSize {
        // hashstr,category 
        cat := categories[int(rand.Int31n(int32(len(categories))))]
        hashstr := hashstrs[int(rand.Int31n(int32(len(hashstrs))))]
        line := "\""+hashstr+"\",\""+cat+"\"\n"
        n, err := w.WriteString(line)
        if err != nil {
            fmt.Println(n, err)
            return
        }
        size += int64(len(line))
    }
    err = w.Flush()
    if err != nil {
        fmt.Println(err)
        return
    }
    err = f.Close()
    if err != nil {
        fmt.Println(err)
        return
    }
    fmt.Println("Size:", size)
}