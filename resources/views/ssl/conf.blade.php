[ req]
distinguished_name = req_distinguished_name
req_extensions = v3_req

[req_distinguished_name]
countryName = Country Name (2 letter code)
countryName_default = GB
stateOrProvinceName = State or Province Name (full name)
stateOrProvinceName_default = Berks
localityName = Locality Name (eg, city)
localityName_default = Reading
organizationalUnitName = Organizational Unit Name (eg, section)
organizationalUnitName_default = Developers
commonName = {{ $url }}
commonName_max	= 64

[ v3_req ]
# Extensions to add to a certificate request
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = {{ $url }}
DNS.2 = *.{{ $url }}
