@echo Resetto le impostanzioni LPT1...
net use LPT1 /Delete
@echo Imposto la stampante LPT1 su olivetti posto2
NET use LPT1: \\POSTO2\OLIVETTI309 /persistent:yes

@echo Imposto la stampante LPT3 su zebra
@NET use LPT1: \\POSTO2\ZebraZ4M /persistent:yes