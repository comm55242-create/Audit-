package com.melcomgroup.zahed.melcomshopaudit;

import com.google.gson.annotations.SerializedName;
import java.io.Serializable;

/* JADX INFO: loaded from: classes.dex */
public class Input_values implements Serializable {

    @SerializedName("BARCODE")
    private String BARCODE;

    @SerializedName("CURR_STOCK")
    private String CURR_STOCK;

    @SerializedName("DEPT")
    private String DEPT;

    @SerializedName("ITEM_CODE")
    private String ITEM_CODE;

    @SerializedName("ITEM_NAME")
    private String ITEM_NAME;

    @SerializedName("PRICE")
    private String PRICE;

    @SerializedName("SHOP_CODE")
    private String SHOP_CODE;

    public String getITEM_CODE() {
        return this.ITEM_CODE;
    }

    public void setITEM_CODE(String ITEM_CODE) {
        this.ITEM_CODE = ITEM_CODE;
    }

    public String getITEM_NAME() {
        return this.ITEM_NAME;
    }

    public void setITEM_NAME(String ITEM_NAME) {
        this.ITEM_NAME = ITEM_NAME;
    }

    public String getBARCODE() {
        return this.BARCODE;
    }

    public void setBARCODE(String BARCODE) {
        this.BARCODE = BARCODE;
    }

    public String getPRICE() {
        return this.PRICE;
    }

    public void setPRICE(String PRICE) {
        this.PRICE = PRICE;
    }

    public String getDEPT() {
        return this.DEPT;
    }

    public void setDEPT(String DEPT) {
        this.DEPT = DEPT;
    }

    public String getSHOP_CODE() {
        return this.SHOP_CODE;
    }

    public void setSHOP_CODE(String SHOP_CODE) {
        this.SHOP_CODE = SHOP_CODE;
    }

    public String getCURR_STOCK() {
        return this.CURR_STOCK;
    }

    public void setCURR_STOCK(String CURR_STOCK) {
        this.CURR_STOCK = CURR_STOCK;
    }
}
